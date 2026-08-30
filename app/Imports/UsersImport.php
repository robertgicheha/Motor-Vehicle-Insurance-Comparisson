<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\CSV\Reader as CsvReader;

class UsersImport
{
    protected array $errors = [];
    protected int $imported = 0;

    public function import(string $filePath, string $type = 'xlsx'): array
    {
        $this->errors = [];
        $this->imported = 0;

        $reader = $type === 'csv' ? new CsvReader : new Reader;
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) continue;

                $cells = $row->toArray();
                $data = [
                    'first_name' => $cells[0] ?? '',
                    'last_name' => $cells[1] ?? '',
                    'email' => $cells[2] ?? '',
                    'mobile' => $cells[3] ?? '',
                    'gender' => strtolower($cells[4] ?? ''),
                    'password' => $cells[5] ?? 'password',
                ];

                $validator = Validator::make($data, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'mobile' => 'required|string|max:20',
                    'gender' => 'nullable|in:male,female,other',
                    'password' => 'required|min:6',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Row {$index}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'email' => $data['email'],
                    'mobile' => $data['mobile'],
                    'gender' => $data['gender'] ?: null,
                    'password' => Hash::make($data['password']),
                    'is_active' => true,
                    'profile_completed' => true,
                ]);

                $user->syncRoles(['user']);
                $this->imported++;
            }
        }

        $reader->close();

        return [
            'imported' => $this->imported,
            'errors' => $this->errors,
        ];
    }
}
