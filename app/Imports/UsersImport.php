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
                    'branch' => $cells[0] ?? '',
                    'password' => $cells[1] ?? 'password',
                ];

                $validator = Validator::make($data, [
                    'branch' => 'required|string|max:255',
                    'password' => 'required|min:12',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Row {$index}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $user = User::create([
                    'username' => User::generateUsername($data['branch'], 'user'),
                    'branch' => $data['branch'],
                    'password' => Hash::make($data['password']),
                    'is_active' => true,
                    'verified_at' => now(),
                    'profile_completed' => true,
                    'password_changed_at' => now(),
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
