<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AvtomobilTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Avtomobillar';
    }

    public function headings(): array
    {
        return [
            '№',
            'Rusumi',
            'Davlat raqami',
            'Ishlab chiqarilgan yili',
            'Biriktirilgan shaxs',
        ];
    }

    public function array(): array
    {
        return [
            [1, 'Damas', '01 A 123 AA', 2020, 'Ism Familiya'],
            [2, 'Cobalt', '01 B 456 BB', 2022, 'Ism Familiya'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 25,
            'C' => 20,
            'D' => 28,
            'E' => 30,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:E1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1F4E79');

        $sheet->getStyle("A1:E1")->getFont()
            ->setBold(true)
            ->setSize(11)
            ->getColor()->setRGB('FFFFFF');

        $sheet->getStyle("A2:E{$lastRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D6E4F0');

        $sheet->getStyle("A1:E{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('B4C6E7');

        return [
            "A1:E{$lastRow}" => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
