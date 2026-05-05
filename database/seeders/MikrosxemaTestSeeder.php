<?php

namespace Database\Seeders;

use App\Models\Mikrosxema;
use App\Models\Station;
use App\Enums\StationType;
use Illuminate\Database\Seeder;

class MikrosxemaTestSeeder extends Seeder
{
    public function run(): void
    {
        $stations = Station::where('type', StationType::ENTERPRISE_PCH->value)->take(2)->get();

        if ($stations->isEmpty()) {
            $this->command?->warn('ENTERPRISE_PCH tipli stansiya topilmadi.');
            return;
        }

        $items = [
            ['nomi' => 'Электростанция',          'count' => 2],
            ['nomi' => 'Релсоьсорезный станок',   'count' => 1],
            ['nomi' => 'Рельсосверлилный станок', 'count' => 3],
            ['nomi' => 'Рельсошлифовальный станок','count' => 1],
            ['nomi' => 'Электошпалоподбойка',     'count' => 2],
            ['nomi' => 'Гидравлический домкрат',  'count' => 4],
        ];

        $holatlar = ['soz', 'nosoz'];
        $bolinmalar = ['1-bo\'linma', '2-bo\'linma', '3-bo\'linma'];

        $created = 0;
        foreach ($stations as $station) {
            Mikrosxema::where('station_id', $station->id)->delete();

            foreach ($items as $item) {
                for ($i = 0; $i < $item['count']; $i++) {
                    Mikrosxema::create([
                        'station_id' => $station->id,
                        'nomi' => $item['nomi'],
                        'texnik_holati' => $holatlar[$i % 2],
                        'biriktirilgan_joyi' => $bolinmalar[$i % 3],
                        'rasmlar' => [],
                    ]);
                    $created++;
                }
            }
        }

        $this->command?->info("{$created} ta kichik mexanizm yaratildi (" . $stations->count() . " stansiya uchun).");
    }
}
