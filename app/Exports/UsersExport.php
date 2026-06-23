<?php

namespace App\Exports;

use App\Models\User;

class UsersExport
{
    public function download($filename = 'users.xlsx')
    {
        $users = User::with('roles')->get();

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $filePath = storage_path('app/' . $filename);
        $writer->openToFile($filePath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Users');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Name', 'Email', 'Mobile', 'Gender', 'Role', 'Status', 'Last Online', 'Registered'
        ]));

        foreach ($users as $user) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $user->name,
                $user->email,
                $user->mobile ?? '',
                $user->gender ?? '',
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
