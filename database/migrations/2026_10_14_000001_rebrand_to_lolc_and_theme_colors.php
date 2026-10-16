<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_BRAND = 'MIC Global Risks';

    private const NEW_BRAND = 'LOLC Microfinance Bank';

    private const NEW_PRIMARY = '#263B8B';

    private const OLD_PRIMARY = '#1756c8';

    public function up(): void
    {
        $this->rebrandColumns('settings', ['value']);
        $this->rebrandColumns('email_settings', ['letterhead_company', 'letterhead_address', 'subject', 'body']);

        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'primary_color')->update(['value' => self::NEW_PRIMARY]);
            DB::table('settings')
                ->whereIn('key', ['company_name', 'mail_from_name'])
                ->update(['value' => self::NEW_BRAND]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'primary_color')->update(['value' => self::OLD_PRIMARY]);
    }

    private function rebrandColumns(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                continue;
            }

            $rows = DB::table($table)
                ->where($column, 'like', '%' . self::OLD_BRAND . '%')
                ->get(['id', $column]);

            foreach ($rows as $row) {
                $value = str_replace(
                    [
                        self::OLD_BRAND . ' Insurance (Kenya) Limited',
                        self::OLD_BRAND . ' (Kenya) Limited',
                        self::OLD_BRAND . ' Limited',
                        self::OLD_BRAND . ' Insurance',
                        self::OLD_BRAND,
                    ],
                    self::NEW_BRAND,
                    (string) $row->{$column}
                );

                DB::table($table)->where('id', $row->id)->update([$column => $value]);
            }
        }
    }
};
