<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Move the users table from email-based to username/branch-based accounts.
     *
     * - Adds username (unique), branch (app-encrypted) and password_changed_at.
     * - Backfills a username for every existing account.
     * - Drops the email columns and the password_reset_tokens table, since
     *   email-based login, self-registration and password reset are removed.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('id');
            }
            if (! Schema::hasColumn('users', 'branch')) {
                $table->text('branch')->nullable()->after('username');
            }
            if (! Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            }
        });

        // `mobile` is now stored via an encrypted cast, so its ciphertext can
        // exceed a VARCHAR(255). Widen it and re-encrypt the existing plaintext
        // values in place (skipping any value that is already encrypted).
        Schema::table('users', function (Blueprint $table) {
            $table->text('mobile')->nullable()->change();
        });

        foreach (DB::table('users')->whereNotNull('mobile')->get() as $user) {
            $value = $user->mobile;
            if ($value === '' || $value === null) {
                continue;
            }

            try {
                decrypt($value, false);
                continue; // already encrypted by a partial prior run
            } catch (\Throwable $e) {
                // plaintext — encrypt below
            }

            DB::table('users')->where('id', $user->id)->update([
                'mobile' => encrypt($value, false),
            ]);
        }

        // Backfill usernames and password timestamps for pre-existing accounts.
        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $base = isset($user->email) && $user->email
                ? Str::before($user->email, '@')
                : 'user';

            $base = Str::slug($base, '_') ?: 'user';

            $username = Str::startsWith($base, 'user') ? $base : 'user_'.$base;
            $suffix = 1;
            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $suffix++;
                $username = $base.'_'.$suffix;
            }

            DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
                'password_changed_at' => $user->password_changed_at
                    ?? $user->updated_at
                    ?? $user->created_at
                    ?? now(),
            ]);
        }

        // Now that every row has one, add the unique index.
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });

        // Drop the email-based columns / tables. Each guarded so a partial
        // prior run (or a fresh install that never had a column) is safe.
        Schema::table('users', function (Blueprint $table) {
            $drop = [];
            foreach (['email', 'email_verified_at', 'pending_email', 'email_verify_token', 'google_id'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $drop[] = $column;
                }
            }

            if (in_array('email', $drop, true)) {
                try {
                    $table->dropUnique(['email']);
                } catch (\Throwable $e) {
                    // Index name may differ across drivers; fall through.
                }
            }

            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });

        Schema::dropIfExists('password_reset_tokens');
    }

    public function down(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('google_id')->nullable();
            $table->string('pending_email')->nullable();
            $table->string('email_verify_token')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'branch', 'password_changed_at']);
        });
    }
};
