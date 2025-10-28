<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            [
                'type' => 'terminal',
                'title' => 'Termez',
                'coordinates' => ['lat' => 37.2242, 'lng' => 67.2783],
                'description' => 'Surxondaryo viloyatining eng yirik temir yo\'l stantsiyasi. Janubiy Oʻzbekistonning asosiy transport hub\'i.',
                'details' => [
                    'platforms' => 6,
                    'tracks' => 8,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'Bufet', 'WC', 'Bagaj xonasi', 'Axborot taxtasi', 'Wi-Fi'],
                    'year_built' => 1929,
                    'reconstruction' => 2018
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=1',
                    'https://picsum.photos/800/600?random=2',
                    'https://picsum.photos/800/600?random=3'
                ]
            ],
            [
                'type' => 'station',
                'title' => 'Hairaton',
                'coordinates' => ['lat' => 37.2367, 'lng' => 67.1078],
                'description' => 'Afgʻoniston Islom Respublikasi bilan chegaradagi muhim stantsiya. Xalqaro yuk tashish uchun strategik ahamiyatga ega.',
                'details' => [
                    'platforms' => 3,
                    'tracks' => 4,
                    'electrified' => true,
                    'facilities' => ['Bojxona nazorati', 'Kassa', 'Kutish zali', 'WC'],
                    'year_built' => 2011,
                    'border_crossing' => true
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=4',
                    'https://picsum.photos/800/600?random=5'
                ]
            ],
            [
                'type' => 'station',
                'title' => 'Jarqoʻrgʻon',
                'coordinates' => ['lat' => 37.5333, 'lng' => 67.4500],
                'description' => 'Surkhan vodiysidagi muhim oraliq stantsiya. Mintaqaviy yoʻlovchi va yuk tashish uchun xizmat koʻrsatadi.',
                'details' => [
                    'platforms' => 3,
                    'tracks' => 4,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'Bufet', 'WC'],
                    'year_built' => 1940
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=6',
                    'https://picsum.photos/800/600?random=7'
                ]
            ],
            [
                'type' => 'station',
                'title' => 'Boysun',
                'coordinates' => ['lat' => 38.2147, 'lng' => 67.2036],
                'description' => 'Boysun tumani markazidagi temir yoʻl stantsiyasi. Tog\'li hududda joylashgan.',
                'details' => [
                    'platforms' => 2,
                    'tracks' => 3,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'WC'],
                    'year_built' => 1942
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=8'
                ]
            ],
            [
                'type' => 'station',
                'title' => 'Denov',
                'coordinates' => ['lat' => 38.2667, 'lng' => 67.9000],
                'description' => 'Surxondaryo viloyatining shimoliy qismidagi yirik stantsiya. Mintaqaviy transport tuguni.',
                'details' => [
                    'platforms' => 4,
                    'tracks' => 5,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'Bufet', 'WC', 'Bagaj xonasi'],
                    'year_built' => 1938
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=9',
                    'https://picsum.photos/800/600?random=10'
                ]
            ],
            [
                'type' => 'station',
                'title' => 'Shoʻrchi',
                'coordinates' => ['lat' => 38.1667, 'lng' => 67.7833],
                'description' => 'Qashqadaryo viloyatining janubidagi stantsiya. Termez - Qarshi yoʻnalishining muhim nuqtasi.',
                'details' => [
                    'platforms' => 3,
                    'tracks' => 4,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'WC'],
                    'year_built' => 1936
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=11'
                ]
            ],
            [
                'type' => 'junction',
                'title' => 'Kumqoʻrgʻon',
                'coordinates' => ['lat' => 38.5333, 'lng' => 66.8167],
                'description' => 'Muhim temir yoʻl tutashuvi. Bir nechta yoʻnalishlar bu yerda kesishadi.',
                'details' => [
                    'platforms' => 5,
                    'tracks' => 7,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'Bufet', 'WC', 'Bagaj xonasi'],
                    'year_built' => 1935,
                    'junction_point' => true
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=12',
                    'https://picsum.photos/800/600?random=13'
                ]
            ],
            [
                'type' => 'station',
                'title' => 'Gʻuzor',
                'coordinates' => ['lat' => 38.6167, 'lng' => 66.2500],
                'description' => 'Qashqadaryo viloyatining gʻarbiy qismidagi stantsiya. Tog\'li hudud yoʻnalishlari uchun muhim.',
                'details' => [
                    'platforms' => 3,
                    'tracks' => 4,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'Bufet', 'WC'],
                    'year_built' => 1934
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=14'
                ]
            ],
            [
                'type' => 'terminal',
                'title' => 'Qarshi',
                'coordinates' => ['lat' => 38.8644, 'lng' => 65.7889],
                'description' => 'Qashqadaryo viloyatining markazi. Yirik transport-logistika hub\'i va yo\'lovchi stantsiyasi.',
                'details' => [
                    'platforms' => 8,
                    'tracks' => 12,
                    'electrified' => true,
                    'facilities' => ['Kutish zali', 'Kassa', 'Bufet', 'WC', 'Bagaj xonasi', 'Axborot taxtasi', 'Wi-Fi', 'Mehmonxona'],
                    'year_built' => 1916,
                    'reconstruction' => 2020
                ],
                'images' => [
                    'https://picsum.photos/800/600?random=15',
                    'https://picsum.photos/800/600?random=16',
                    'https://picsum.photos/800/600?random=17',
                    'https://picsum.photos/800/600?random=18'
                ]
            ]
        ];

        foreach ($stations as $station) {
            Station::create($station);
        }
    }
}