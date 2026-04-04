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

class MikrosxemaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Kichik mexanizmlar';
    }

    public function headings(): array
    {
        return [
            '№',
            'Nomi',
            'Texnik holati',
            'Biriktirilgan joyi',
        ];
    }

    public function array(): array
    {
        return [
            [1, 'Mexanizm nomi', 'Yaxshi', '1-sexda'],
            [2, 'Mexanizm nomi', 'Ishlamoqda', '2-sexda'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 25,
            'D' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:D1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2E75B6');

        $sheet->getStyle("A1:D1")->getFont()
            ->setBold(true)
            ->setSize(11)
            ->getColor()->setRGB('FFFFFF');

        $sheet->getStyle("A2:D{$lastRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D6E4F0');

        $sheet->getStyle("A1:D{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('B4C6E7');

        return [
            "A1:D{$lastRow}" => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
