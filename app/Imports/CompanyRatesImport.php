<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Rate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\CSV\Reader as CsvReader;

class CompanyRatesImport
{
    protected array $errors = [];
    protected array $results = [];

    protected array $categoryMap = [
        'Windscreen' => 'Cover Limits',
        'Car Entertainment' => 'Cover Limits',
        'Towing' => 'Cover Limits',
        'Authorized Repair' => 'Cover Limits',
        'Medical Expenses' => 'Cover Limits',
        'Geographical Area' => 'Additional Cover',
        'Third Party Bodily Injury -Per Person' => 'Third Party Limits',
        'Third Party Bodily Injury - Per Event' => 'Third Party Limits',
        'Passenger Liability Limit any one person' => 'Third Party Limits',
        'Passenger Liability Limit any one event' => 'Third Party Limits',
        'Third Party Property Damage Limit' => 'Third Party Limits',
        'Own Damage' => 'Excess',
        'Partial Theft' => 'Excess',
        'Theft With Anti-Theft Device' => 'Excess',
        'Theft Without Anti-Theft Device' => 'Excess',
        'Theft with a tracking device' => 'Excess',
        'Third Party Bodily Injury' => 'Excess',
        'Third Party Property Damage' => 'Excess',
        'Novice' => 'Excess',
        'Premium Rate' => 'Premium Rate',
        'PLL' => 'Premium Rate',
        'PVT' => 'Premium Rate',
        'Including special perils' => 'Additional Cover',
        'Motor trade' => 'Additional Cover',
        'No blame' => 'Additional Cover',
        'Riot' => 'Additional Cover',
        'Fatal Personal Accident' => 'Additional Cover',
        'Minimum Sum Insured' => 'Additional Cover',
        'Maximum Age' => 'Additional Cover',
        'Excess Protector' => 'Additional Cover',
        'TPO' => 'Additional Cover',
    ];

    public function import(string $filePath, string $type = 'xlsx'): array
    {
        $this->errors = [];
        $this->results = [];

        $reader = $type === 'csv' ? new CsvReader : new Reader;
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            $rows = [];
            foreach ($sheet->getRowIterator() as $index => $row) {
                $rows[] = $row->toArray();
            }

            if (count($rows) < 2) {
                $this->errors[] = 'Sheet has insufficient data';
                break;
            }

            $headers = $rows[0];
            $companyNames = array_slice($headers, 1);
            $companyHeaders = [];
            foreach ($companyNames as $i => $name) {
                $col = $i + 1;
                $companyHeaders[$col] = ['name' => trim((string) $name), 'col' => $col];
            }

            $companyColMap = [];
            foreach ($companyHeaders as $ch) {
                if (!empty($ch['name'])) {
                    $companyColMap[$ch['col']] = $ch['name'];
                }
            }

            for ($i = 1; $i < count($rows); $i++) {
                $rowData = $rows[$i];
                $itemLabel = trim($rowData[0] ?? '');
                if (empty($itemLabel)) continue;

                $category = $this->inferCategory($itemLabel);

                foreach ($companyColMap as $col => $companyName) {
                    $rawValue = $rowData[$col] ?? '';

                    if (!isset($this->results[$companyName])) {
                        $this->results[$companyName] = [];
                    }

                    $this->results[$companyName][] = [
                        'category' => $category,
                        'item_name' => $itemLabel,
                        'raw_value' => trim((string) $rawValue),
                    ];
                }
            }
        }

        $reader->close();

        $imported = 0;
        $created = [];

        foreach ($this->results as $companyName => $rates) {
            $company = Company::firstOrCreate(
                ['name' => $companyName],
                [
                    'slug' => Str::slug($companyName),
                    'is_active' => true,
                ]
            );

            if ($company->wasRecentlyCreated) {
                $created[] = $companyName;
            }

            foreach ($rates as $rateData) {
                $parsed = $this->parseRateValue($rateData['raw_value']);
                $data = [
                    'company_id' => $company->id,
                    'category' => $rateData['category'],
                    'item_name' => $rateData['item_name'],
                    'value_type' => $parsed['value_type'],
                    'value' => $parsed['value'],
                    'percentage' => $parsed['percentage'],
                    'min_value' => $parsed['min_value'],
                    'min_premium' => $parsed['min_premium'],
                    'notes' => $parsed['notes'],
                ];

                $validator = Validator::make($data, [
                    'category' => 'required|string|max:255',
                    'item_name' => 'required|string|max:255',
                    'value_type' => 'required|string|in:fixed,percentage,percentage_min,text',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "{$companyName} / {$rateData['item_name']}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                Rate::create($data);
                $imported++;
            }
        }

        return [
            'imported' => $imported,
            'companies_created' => $created,
            'errors' => $this->errors,
        ];
    }

    protected function inferCategory(string $itemLabel): string
    {
        foreach ($this->categoryMap as $key => $category) {
            if (str_contains($itemLabel, $key)) {
                return $category;
            }
        }

        if (preg_match('/min.*premium/i', $itemLabel) || preg_match('/^premium/i', $itemLabel)) {
            return 'Premium Rate';
        }

        return 'Additional Cover';
    }

    protected function parseRateValue(string $value): array
    {
        $value = trim($value);
        $result = [
            'value_type' => 'text',
            'value' => null,
            'percentage' => null,
            'min_value' => null,
            'min_premium' => null,
            'notes' => $value ?: null,
        ];

        if (empty($value)) {
            return $result;
        }

        if (preg_match('/^No Cover/i', $value) || preg_match('/^Covered/i', $value) || preg_match('/^Not Covered/i', $value)) {
            $result['value_type'] = 'text';
            $result['notes'] = $value;
            return $result;
        }

        if (preg_match('/([\d.]+)%\s*[mM]in[.\s]*[Kk]?[Ee]?[Ss]?h?\.?\s*([\d,]+)/', $value, $m)) {
            $result['value_type'] = 'percentage_min';
            $result['percentage'] = (float) $m[1];
            $result['min_value'] = (float) str_replace(',', '', $m[2]);
            $result['notes'] = $value;
            return $result;
        }

        if (preg_match('/([\d.]+)%/', $value, $m)) {
            $result['value_type'] = 'percentage';
            $result['percentage'] = (float) $m[1];

            if (preg_match('/[mM]in[.\s]*[Pp]remium[.\s]*-?\s*[Kk]?[Ee]?[Ss]?h?\.?\s*([\d,]+)/', $value, $pm)) {
                $result['min_premium'] = (float) str_replace(',', '', $pm[1]);
            }

            return $result;
        }

        if (preg_match('/[Kk]?[Ee]?[Ss]?h?\.?\s*([\d,]+)\s*\/?\s*-/', $value, $m)) {
            $result['value_type'] = 'fixed';
            $result['value'] = (float) str_replace(',', '', $m[1]);
            return $result;
        }

        if (preg_match('/([\d,]+)\s*additional/i', $value, $m)) {
            $result['value_type'] = 'fixed';
            $result['value'] = (float) str_replace(',', '', $m[1]);
            $result['notes'] = $value;
            return $result;
        }

        if (preg_match('/[Kk]?[Ee]?[Ss]?h?\.?\s*([\d,]+)/', $value, $m)) {
            $result['value_type'] = 'fixed';
            $result['value'] = (float) str_replace(',', '', $m[1]);
            return $result;
        }

        return $result;
    }
}
