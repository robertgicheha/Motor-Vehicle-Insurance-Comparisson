<?php

namespace App\Exports;

use App\Models\User;

class UsersExport
{
    public function download($filename = 'users.xlsx', bool $usersOnly = false)
    {
        $query = User::with('roles');
        if ($usersOnly) {
            $query->whereHas('roles', fn($q) => $q->where('name', 'user'));
        }
        $users = $query->get();

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $filePath = storage_path('app/' . $filename);
        $writer->openToFile($filePath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Users');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Username', 'Branch', 'Role', 'Status', 'Last Online', 'Registered'
        ]));

        foreach ($users as $user) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $user->username,
                $user->branch ?? '',
                $user->roles->pluck('name')->implode(', '),
                $user->is_active ? 'Active' : 'Inactive',
                $user->last_online_at ? $user->last_online_at->format('Y-m-d H:i') : 'Never',
                $user->created_at->format('Y-m-d'),
            ]));
        }

        $writer->close();
        return $filePath;
    }
}
