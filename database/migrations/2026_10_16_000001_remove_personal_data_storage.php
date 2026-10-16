<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data-protection migration: the platform is hosted outside Kenya (London
 * data centre) and must not persist the personal data of vehicle owners or
 * staff. This drops the personal/contact columns that were used to identify
 * natural persons and purges the copies that already existed in logs,
 * notifications and value-snapshot columns.
 *
 * Vehicle identity (registration / chassis / engine number, make, model,
 * year, body type, valuation) and reference numbers (quotation, sticker,
 * claim) remain the way records are identified from now on.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->purgeStoredPersonalData();

        // Email logs previously stored the recipient/sender addresses and the
        // full rendered body (which named the customer).
        Schema::table('sticker_email_logs', function (Blueprint $table) {
            $table->dropColumn(['recipient', 'sender', 'body']);
        });

        Schema::table('claim_email_logs', function (Blueprint $table) {
            $table->dropColumn(['recipient', 'cc', 'sender']);
        });

        // Owner / customer identity on stickers, claims and quotations.
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_email', 'customer_phone', 'email_body', 'email_from_name']);
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_email', 'customer_phone']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'email']);
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['name', 'email']);
        });

        // Staff personal identity. Accounts are identified by the non-personal
        // username only.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'name', 'gender', 'mobile']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->text('mobile')->nullable();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('email')->nullable();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('salutation')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
        });

        Schema::table('stickers', function (Blueprint $table) {
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('email_body')->nullable();
            $table->string('email_from_name')->nullable();
        });

        Schema::table('sticker_email_logs', function (Blueprint $table) {
            $table->string('recipient')->nullable();
            $table->string('sender')->nullable();
            $table->text('body')->nullable();
        });

        Schema::table('claim_email_logs', function (Blueprint $table) {
            $table->string('recipient')->nullable();
            $table->string('cc')->nullable();
            $table->string('sender')->nullable();
        });
    }

    /**
     * Purge personal data that is already stored in free-text, JSON and
     * serialized columns before the structured columns are dropped.
     */
    protected function purgeStoredPersonalData(): void
    {
        // Notifications are transient alerts that embedded names/emails in
        // their serialized payload; drop them rather than attempt to rewrite.
        DB::table('notifications')->delete();

        // Repoint the actor snapshots on log tables at the non-personal
        // username (accounts are username-only from now on).
        DB::table('users')->select('id', 'username')->orderBy('id')->chunk(200, function ($users) {
            foreach ($users as $user) {
                if ($user->username === null) {
                    continue;
                }

                DB::table('audit_logs')->where('user_id', $user->id)->update(['user_name' => $user->username]);
                DB::table('activity_logs')->where('user_id', $user->id)->update(['user_name' => $user->username]);
            }
        });

        // Replace personal names embedded in free-text activity descriptions
        // with the corresponding username.
        $this->replaceNamesInActivityDescriptions();

        // Strip personal keys out of the JSON value snapshots kept on the
        // audit trail.
        $this->scrubAuditLogValues();
    }

    protected function replaceNamesInActivityDescriptions(): void
    {
        $map = [];
        DB::table('users')
            ->select('name', 'first_name', 'last_name', 'username')
            ->get()
            ->each(function ($user) use (&$map) {
                if (! $user->username) {
                    return;
                }

                $candidates = array_filter([
                    trim((string) $user->name),
                    trim(((string) $user->first_name) . ' ' . ((string) $user->last_name)),
                ]);

                foreach ($candidates as $candidate) {
                    if ($candidate !== '') {
                        $map[$candidate] = $user->username;
                    }
                }
            });

        if ($map === []) {
            return;
        }

        // Replace longer names first so a shorter name cannot partially
        // clobber a longer one.
        uksort($map, fn ($a, $b) => strlen($b) <=> strlen($a));

        DB::table('activity_logs')->select('id', 'description')->orderBy('id')->chunk(200, function ($rows) use ($map) {
            foreach ($rows as $row) {
                if ($row->description === null) {
                    continue;
                }

                $updated = str_replace(array_keys($map), array_values($map), $row->description);

                if ($updated !== $row->description) {
                    DB::table('activity_logs')->where('id', $row->id)->update(['description' => $updated]);
                }
            }
        });
    }

    protected function scrubAuditLogValues(): void
    {
        $keysByModel = [
            'User' => ['first_name', 'last_name', 'name', 'full_name', 'gender', 'mobile', 'email'],
            'Quotation' => ['salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'email'],
            'Sticker' => ['customer_name', 'customer_email', 'customer_phone', 'email_body', 'email_from_name'],
            'Claim' => ['customer_name', 'customer_email', 'customer_phone'],
            'Enquiry' => ['name', 'email'],
        ];

        $fallback = [
            'salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'mobile', 'email',
            'customer_name', 'customer_email', 'customer_phone',
            'customer_salutation', 'customer_first_name', 'customer_last_name',
            'email_body', 'email_from_name', 'full_name',
        ];

        DB::table('audit_logs')
            ->select('id', 'model', 'old_values', 'new_values')
            ->orderBy('id')
            ->chunk(200, function ($rows) use ($keysByModel, $fallback) {
                foreach ($rows as $row) {
                    $keys = $keysByModel[$row->model] ?? $fallback;

                    $old = $this->stripKeys($row->old_values, $keys);
                    $new = $this->stripKeys($row->new_values, $keys);

                    if ($old !== $row->old_values || $new !== $row->new_values) {
                        DB::table('audit_logs')->where('id', $row->id)->update([
                            'old_values' => $old,
                            'new_values' => $new,
                        ]);
                    }
                }
            });
    }

    protected function stripKeys($json, array $keys): ?string
    {
        if ($json === null || $json === '') {
            return $json;
        }

        $data = json_decode($json, true);

        if (! is_array($data)) {
            return $json;
        }

        foreach ($keys as $key) {
            unset($data[$key]);
        }

        return json_encode($data);
    }
};
