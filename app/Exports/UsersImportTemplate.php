<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersImportTemplate implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Mario',
                'Rossi',
                'mario.rossi@azienda.com',
                'Azienda SRL',
                'Sviluppatore',
                'false',
                'true',
                'true',
                'password123'
            ],
            [
                'Giulia',
                'Bianchi',
                'giulia.bianchi@azienda.com',
                'Azienda SRL',
                'Project Manager',
                'false',
                'false',
                'true',
                'password456'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nome',
            'cognome',
            'email',
            'azienda',
            'professione',
            'admin',
            'consenso_marketing',
            'consenso_privacy',
            'password'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
