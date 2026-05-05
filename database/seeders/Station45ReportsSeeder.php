<?php

namespace Database\Seeders;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Station45ReportsSeeder extends Seeder
{
    public function run(): void
    {
        $stationId = 45;

        Report::where('station_id', $stationId)->delete();

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->startOfMonth()->format('Y-m-d');
        }

        $datasets = [
            'yuk_ortilishi' => [
                'planned' => [800, 850, 900, 950, 1000, 1050, 1100, 1080, 1150, 1200, 1250, 1300],
                'actual'  => [780, 870, 880, 970, 1020, 1010, 1130, 1050, 1190, 1180, 1280, 1320],
            ],
            'yuk_tushurilishi' => [
                'planned' => [600, 620, 650, 680, 700, 720, 750, 780, 800, 820, 850, 880],
                'actual'  => [580, 640, 630, 700, 690, 740, 730, 800, 790, 840, 830, 900],
            ],
            'pul_tushumi' => [
                'planned' => [35_000_000, 38_000_000, 40_000_000, 42_000_000, 45_000_000, 48_000_000, 50_000_000, 52_000_000, 55_000_000, 58_000_000, 60_000_000, 65_000_000],
                'actual'  => [33_000_000, 39_500_000, 38_000_000, 43_500_000, 44_000_000, 49_000_000, 48_500_000, 53_000_000, 54_000_000, 59_500_000, 61_000_000, 64_000_000],
            ],
            'xarajat_daromad' => [
                'expense' => [22_000_000, 24_000_000, 23_500_000, 26_000_000, 27_500_000, 28_000_000, 30_000_000, 31_500_000, 32_000_000, 34_000_000, 35_500_000, 37_000_000],
                'income'  => [35_000_000, 38_000_000, 40_000_000, 42_000_000, 45_000_000, 48_000_000, 50_000_000, 52_000_000, 55_000_000, 58_000_000, 60_000_000, 65_000_000],
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

        $this->command?->info("Station 45 uchun {$created} ta hisobot yaratildi (12 oy × 4 tur).");
    }
}
