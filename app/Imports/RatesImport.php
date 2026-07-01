<?php

namespace App\Imports;

use App\Models\Rate;
use Illuminate\Support\Facades\Validator;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\CSV\Reader as CsvReader;

class RatesImport
{
    protected array $errors = [];
    protected int $imported = 0;

    public function import(string $filePath, int $companyId, string $type = 'xlsx'): array
    {
        $this->errors = [];
        $this->imported = 0;

        if ($type === 'json') {
            return $this->importJson($filePath, $companyId);
        }

        $reader = $type === 'csv' ? new CsvReader : new Reader;
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) continue;

                $cells = $row->toArray();
                $data = [
                    'company_id' => $companyId,
                    'category' => $cells[0] ?? '',
                    'item_name' => $cells[1] ?? '',
                    'value_type' => $cells[2] ?? 'fixed',
                    'value' => $this->parseNumber($cells[3] ?? ''),
                    'percentage' => $this->parseNumber($cells[4] ?? ''),
                    'min_value' => $this->parseNumber($cells[5] ?? ''),
                    'min_premium' => $this->parseNumber($cells[6] ?? ''),
                    'notes' => $cells[7] ?? '',
                ];

                $this->processRow($data, $index);
            }
        }

        $reader->close();

        return [
            'imported' => $this->imported,
            'errors' => $this->errors,
        ];
    }

    public function importJson(string $filePath, int $companyId): array
    {
        $content = file_get_contents($filePath);
        $rows = json_decode($content, true);

        if (!is_array($rows)) {
            return ['imported' => 0, 'errors' => ['Invalid JSON format']];
        }

        foreach ($rows as $index => $row) {
            $data = [
                'company_id' => $companyId,
                'category' => $row['category'] ?? '',
                'item_name' => $row['item_name'] ?? '',
                'value_type' => $row['value_type'] ?? 'fixed',
                'value' => $this->parseNumber($row['value'] ?? ''),
                'percentage' => $this->parseNumber($row['percentage'] ?? ''),
                'min_value' => $this->parseNumber($row['min_value'] ?? ''),
                'min_premium' => $this->parseNumber($row['min_premium'] ?? ''),
                'notes' => $row['notes'] ?? '',
            ];

            $this->processRow($data, $index + 1);
        }

        return [
            'imported' => $this->imported,
            'errors' => $this->errors,
        ];
    }

    protected function processRow(array $data, int $rowIndex): void
    {
        $validator = Validator::make($data, [
            'category' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'value_type' => 'required|string|in:fixed,percentage,percentage_min,text',
            'value' => 'nullable|numeric',
            'percentage' => 'nullable|numeric|max:100',
            'min_value' => 'nullable|numeric',
            'min_premium' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            $this->errors[] = "Row {$rowIndex}: " . implode(', ', $validator->errors()->all());
            return;
        }

        Rate::create($data);
        $this->imported++;
    }

    protected function parseNumber(string $value): ?float
    {
        $cleaned = preg_replace('/[^0-9.\-]/', '', $value);
        return $cleaned !== '' ? (float) $cleaned : null;
    }
}
