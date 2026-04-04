<?php

namespace App\Exports;

use App\Models\Mikrosxema;
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

class MikrosxemaExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping, WithTitle
{
    protected int $stationId;
    protected int $row = 0;

    public function __construct(int $stationId)
    {
        $this->stationId = $stationId;
    }

    public function title(): string
    {
        return 'Kichik mexanizmlar';
    }

    public function collection()
    {
        return Mikrosxema::where('station_id', $this->stationId)->get();
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

    public function map($mikrosxema): array
    {
        $this->row++;

        return [
            $this->row,
            $mikrosxema->nomi,
            $mikrosxema->texnik_holati,
            $mikrosxema->biriktirilgan_joyi,
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
        $lastRow = max($sheet->getHighestRow(), 2);

        $sheet->getStyle("A1:D1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2E75B6');

        $sheet->getStyle("A1:D1")->getFont()
            ->setBold(true)
            ->setSize(11)
            ->getColor()->setRGB('FFFFFF');

        if ($lastRow > 1) {
            $sheet->getStyle("A2:C{$lastRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D6E4F0');
        }

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
