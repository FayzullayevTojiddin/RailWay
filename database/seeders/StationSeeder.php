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
        'type' => 'station',
        'title' => 'Akbarobod',
        'coordinates' => ['lat' => 38.4500, 'lng' => 66.8500, 'x' => 18.0, 'y' => 10.0],
        'description' => 'Akbarobod stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Aknazar',
        'coordinates' => ['lat' => 38.3000, 'lng' => 66.9500, 'x' => 26.96, 'y' => 18.0],
        'description' => 'Aknazar stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Shurab',
        'coordinates' => ['lat' => 38.2200, 'lng' => 67.0500, 'x' => 32.12, 'y' => 21.75],
        'description' => 'Shurab stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Darband',
        'coordinates' => ['lat' => 38.1000, 'lng' => 67.2500, 'x' => 40.86, 'y' => 26.47],
        'description' => 'Darband stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Boysun',
        'coordinates' => ['lat' => 38.2111, 'lng' => 67.2086, 'x' => 46.24, 'y' => 24.69],
        'description' => 'Boysun stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Pulxakim',
        'coordinates' => ['lat' => 38.0500, 'lng' => 67.3500, 'x' => 51.75, 'y' => 28.56],
        'description' => 'Pulxakim stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Tangimush',
        'coordinates' => ['lat' => 37.9500, 'lng' => 67.4500, 'x' => 55.52, 'y' => 32.99],
        'description' => 'Tangimush stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Aqdajar',
        'coordinates' => ['lat' => 37.8500, 'lng' => 67.5500, 'x' => 60.06, 'y' => 38.82],
        'description' => 'Aqdajar stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Qumqorgon',
        'coordinates' => ['lat' => 38.0500, 'lng' => 67.5500, 'x' => 64.56, 'y' => 42.19],
        'description' => 'Qumqorgon stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Elbayon',
        'coordinates' => ['lat' => 37.7500, 'lng' => 67.7500, 'x' => 68.08, 'y' => 34.23],
        'description' => 'Elbayon stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Shorchi',
        'coordinates' => ['lat' => 38.0100, 'lng' => 67.7900, 'x' => 72.34, 'y' => 30.26],
        'description' => 'Shorchi stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Xayrabod',
        'coordinates' => ['lat' => 38.2000, 'lng' => 67.8500, 'x' => 75.86, 'y' => 22.96],
        'description' => 'Xayrabod stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Danau',
        'coordinates' => ['lat' => 38.2731, 'lng' => 67.8997, 'x' => 77.28, 'y' => 14.76],
        'description' => 'Danau stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Sariasiya',
        'coordinates' => ['lat' => 37.9333, 'lng' => 67.6167, 'x' => 80.95, 'y' => 8.88],
        'description' => 'Sariasiya stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Quduqli',
        'coordinates' => ['lat' => 37.6500, 'lng' => 67.8000, 'x' => 85.16, 'y' => 5.18],
        'description' => 'Quduqli stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Surxan',
        'coordinates' => ['lat' => 37.7000, 'lng' => 67.5800, 'x' => 61.54, 'y' => 48.46],
        'description' => 'Surxan stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Zartepa',
        'coordinates' => ['lat' => 37.8500, 'lng' => 67.9500, 'x' => 59.74, 'y' => 55.5],
        'description' => 'Zartepa stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Jarqorgon',
        'coordinates' => ['lat' => 37.4367, 'lng' => 67.4500, 'x' => 60.1, 'y' => 67.78],
        'description' => 'Jarqorgon stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Baqtriya',
        'coordinates' => ['lat' => 37.3500, 'lng' => 67.3500, 'x' => 58.84, 'y' => 75.7],
        'description' => 'Baqtriya stansiyasi'
    ],
    [
        'type' => 'terminal',
        'title' => 'Termez',
        'coordinates' => ['lat' => 37.2242, 'lng' => 67.2783, 'x' => 56.6, 'y' => 87.35],
        'description' => 'Termez - asosiy terminal stansiya'
    ],
    [
        'type' => 'station',
        'title' => 'Galaba',
        'coordinates' => ['lat' => 37.4883, 'lng' => 67.8167, 'x' => 65.74, 'y' => 84.21],
        'description' => 'Galaba stansiyasi'
    ],
    [
        'type' => 'border_station',
        'title' => 'Xayraton',
        'coordinates' => ['lat' => 37.1667, 'lng' => 67.2500, 'x' => 66.98, 'y' => 89.95],
        'description' => 'Xayraton - chegara stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Amuzang',
        'coordinates' => ['lat' => 37.3333, 'lng' => 67.8500, 'x' => 81.82, 'y' => 84.72],
        'description' => 'Amuzang stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Port',
        'coordinates' => ['lat' => 37.2500, 'lng' => 67.3000, 'x' => 58.53, 'y' => 96.15],
        'description' => 'Port stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Uchqizil',
        'coordinates' => ['lat' => 37.5500, 'lng' => 67.5000, 'x' => 50.63, 'y' => 82.9],
        'description' => 'Uchqizil stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Naushaxar',
        'coordinates' => ['lat' => 37.6500, 'lng' => 67.4500, 'x' => 45.38, 'y' => 80.87],
        'description' => 'Naushaxar stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Werobod',
        'coordinates' => ['lat' => 37.7500, 'lng' => 67.4000, 'x' => 38.22, 'y' => 80.56],
        'description' => 'Werobod stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Boldir',
        'coordinates' => ['lat' => 38.0000, 'lng' => 67.7000, 'x' => 25.22, 'y' => 84.19],
        'description' => 'Boldir stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Sement zavod',
        'coordinates' => ['lat' => 37.8000, 'lng' => 66.9500, 'x' => 28.49, 'y' => 68.70],
        'description' => 'Sement zavod stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => '23 km',
        'coordinates' => ['lat' => 38.1000, 'lng' => 67.0500, 'x' => 26.50, 'y' => 73.5],
        'description' => '23 km stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Surxonobod',
        'coordinates' => ['lat' => 38.3500, 'lng' => 68.0000, 'x' => 18.23, 'y' => 86.34],
        'description' => 'Surxonobod stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'RZD',
        'coordinates' => ['lat' => 38.4500, 'lng' => 68.1000, 'x' => 12.18, 'y' => 87.81],
        'description' => 'RZD stansiyasi'
    ],
    [
        'type' => 'station',
        'title' => 'Kelif',
        'coordinates' => ['lat' => 38.5500, 'lng' => 68.2000, 'x' => 6.49, 'y' => 92.32],
        'description' => 'Kelif stansiyasi'
    ]
];

        foreach ($stations as $station) {
            Station::create($station);
        }
    }
}