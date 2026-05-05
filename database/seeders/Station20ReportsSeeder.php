<?php

namespace Database\Seeders;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Station20ReportsSeeder extends Seeder
{
    public function run(): void
    {
        $stationId = 20;

        Report::where('station_id', $stationId)->delete();

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->startOfMonth()->format('Y-m-d');
        }

        $datasets = [
            'yuk_ortilishi' => [
                'planned' => [1200, 1300, 1250, 1400, 1500, 1450, 1600, 1700, 1650, 1800, 1900, 2000],
                'actual'  => [1100, 1280, 1310, 1380, 1620, 1400, 1580, 1750, 1620, 1820, 1850, 2050],
            ],
            'yuk_tushurilishi' => [
                'planned' => [900, 950, 1000, 1050, 1100, 1080, 1150, 1200, 1180, 1250, 1300, 1350],
                'actual'  => [850, 970, 1020, 1010, 1130, 1050, 1190, 1180, 1220, 1240, 1330, 1300],
            ],
            'pul_tushumi' => [
                'planned' => [50_000_000, 55_000_000, 52_000_000, 60_000_000, 65_000_000, 62_000_000, 70_000_000, 75_000_000, 72_000_000, 80_000_000, 85_000_000, 90_000_000],
                'actual'  => [48_000_000, 56_500_000, 54_000_000, 58_000_000, 67_000_000, 60_000_000, 72_000_000, 76_500_000, 70_000_000, 82_000_000, 88_000_000, 89_500_000],
            ],
            'xarajat_daromad' => [
                'expense' => [30_000_000, 32_000_000, 31_000_000, 35_000_000, 38_000_000, 36_000_000, 40_000_000, 42_000_000, 41_000_000, 45_000_000, 47_000_000, 50_000_000],
                'income'  => [50_000_000, 55_000_000, 52_000_000, 60_000_000, 65_000_000, 62_000_000, 70_000_000, 75_000_000, 72_000_000, 80_000_000, 85_000_000, 90_000_000],
            ],
        ];

        $created = 0;
        foreach ($months as $i => $date) {
            foreach ($datasets as $type => $values) {
                $data = [
                    'station_id' => $stationId,
                    'type' => $type,
                    'date' => $date,
                    'notes' => 'Test ma\'lumot — ' . Carbon::parse($date)->format('m.Y'),
                ];

                if ($type === 'xarajat_daromad') {
                    $data['expense'] = $values['expense'][$i];
                    $data['income'] = $values['income'][$i];
                } else {
                    $data['planned_value'] = $values['planned'][$i];
                    $data['actual_value'] = $values['actual'][$i];
                }

                Report::create($data);
                $created++;
            }
        }

        $this->command?->info("Station 20 (Termiz) uchun {$created} ta hisobot yaratildi (12 oy × 4 tur).");
    }
}
