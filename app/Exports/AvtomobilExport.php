<?php

namespace App\Exports;

use App\Models\Avtomobil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AvtomobilExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping, WithTitle
{
    protected int $stationId;
    protected int $row = 0;

    public function __construct(int $stationId)
    {
        $this->stationId = $stationId;
    }

    public function title(): string
    {
        return 'Avtomobillar';
    }

    public function collection()
    {
        return Avtomobil::where('station_id', $this->stationId)->get();
    }

    public function headings(): array
    {
        return [
            '№',
            'Rusumi',
            'Davlat raqami',
            'Ishlab chiqarilgan yili',
            'Biriktirilgan shaxs',
            'Texnik holati',
        ];
    }

    public function map($avtomobil): array
    {
        $this->row++;

        return [
            $this->row,
            $avtomobil->rusumi,
            $avtomobil->davlat_raqami,
            $avtomobil->ishlab_chiqarilgan_yili,
            $avtomobil->biriktirilgan_shaxs,
            $avtomobil->texnik_holati,
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
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max($sheet->getHighestRow(), 2);

        $sheet->getStyle("A1:F1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1F4E79');

        $sheet->getStyle("A1:F1")->getFont()
            ->setBold(true)
            ->setSize(11)
            ->getColor()->setRGB('FFFFFF');

        if ($lastRow > 1) {
            $sheet->getStyle("A2:F{$lastRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D6E4F0');
        }

        $sheet->getStyle("A1:F{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('B4C6E7');

        return [
            "A1:F{$lastRow}" => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
