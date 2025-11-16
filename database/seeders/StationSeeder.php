<?php
namespace Database\Seeders;
use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            [ 'type' => 'small_station', 'title' => "Aqrabot",      'coordinates' => ['lat' => 38.4500, 'lng' => 66.8500, 'x' => 18.0, 'y' => 10.0],  'description' => 'Akbarobod stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Oqnazar",      'coordinates' => ['lat' => 38.3000, 'lng' => 66.9500, 'x' => 26.96, 'y' => 18.0],  'description' => 'Aknazar stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Sho'rob",      'coordinates' => ['lat' => 38.2200, 'lng' => 67.0500, 'x' => 32.12, 'y' => 21.75],  'description' => 'Shurab stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Darband",      'coordinates' => ['lat' => 38.1000, 'lng' => 67.2500, 'x' => 40.86, 'y' => 26.47],  'description' => 'Darband stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Boysun",       'coordinates' => ['lat' => 38.2111, 'lng' => 67.2086, 'x' => 46.24, 'y' => 24.69],  'description' => 'Boysun stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Pulhakim",     'coordinates' => ['lat' => 38.0500, 'lng' => 67.3500, 'x' => 51.75, 'y' => 28.56],  'description' => 'Pulxakim stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Tangimush",    'coordinates' => ['lat' => 37.9500, 'lng' => 67.4500, 'x' => 55.52, 'y' => 32.99],  'description' => 'Tangimush stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Oqjar",        'coordinates' => ['lat' => 37.8500, 'lng' => 67.5500, 'x' => 60.06, 'y' => 38.82],  'description' => 'Aqdajar stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Qumqo'rg'on",  'coordinates' => ['lat' => 38.0500, 'lng' => 67.5500, 'x' => 64.56, 'y' => 42.19],  'description' => 'Qumqorgon stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Elbayon",      'coordinates' => ['lat' => 37.7500, 'lng' => 67.7500, 'x' => 68.08, 'y' => 34.23],  'description' => 'Elbayon stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Sho'rchi",     'coordinates' => ['lat' => 38.0100, 'lng' => 67.7900, 'x' => 72.34, 'y' => 30.26],  'description' => 'Shorchi stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Xayrabod",     'coordinates' => ['lat' => 38.2000, 'lng' => 67.8500, 'x' => 75.86, 'y' => 22.96],  'description' => 'Xayrabod stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Denov",        'coordinates' => ['lat' => 38.2731, 'lng' => 67.8997, 'x' => 77.28, 'y' => 14.76],  'description' => 'Danau stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Sariosiyo",    'coordinates' => ['lat' => 37.9333, 'lng' => 67.6167, 'x' => 80.95, 'y' => 8.88],   'description' => 'Sariasiya stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Quduqli",      'coordinates' => ['lat' => 37.6500, 'lng' => 67.8000, 'x' => 85.16, 'y' => 5.18],   'description' => 'Quduqli stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Surxon",       'coordinates' => ['lat' => 37.7000, 'lng' => 67.5800, 'x' => 61.54, 'y' => 48.46],  'description' => 'Surxan stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Zartepa",      'coordinates' => ['lat' => 37.8500, 'lng' => 67.9500, 'x' => 59.74, 'y' => 55.5],   'description' => 'Zartepa stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Jarqo'rg'on",  'coordinates' => ['lat' => 37.4367, 'lng' => 67.4500, 'x' => 60.1, 'y' => 67.78],   'description' => 'Jarqorgon stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Baktriya",     'coordinates' => ['lat' => 37.3500, 'lng' => 67.3500, 'x' => 58.84, 'y' => 75.7],   'description' => 'Baqtriya stansiyasi' ],
            [ 'type' => 'big_station', 'title' => "Termiz",       'coordinates' => ['lat' => 37.2242, 'lng' => 67.2783, 'x' => 56.6, 'y' => 87.35],   'description' => 'Termez - asosiy terminal stansiya' ],
            [ 'type' => 'small_station', 'title' => "G'alaba",      'coordinates' => ['lat' => 37.4883, 'lng' => 67.8167, 'x' => 65.74, 'y' => 84.21],  'description' => 'Galaba stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Xayraton",     'coordinates' => ['lat' => 37.1667, 'lng' => 67.2500, 'x' => 66.98, 'y' => 89.95],  'description' => 'Xayraton - chegara stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Amuzang",      'coordinates' => ['lat' => 37.3333, 'lng' => 67.8500, 'x' => 81.82, 'y' => 84.72],  'description' => 'Amuzang stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Port",         'coordinates' => ['lat' => 37.2500, 'lng' => 67.3000, 'x' => 58.53, 'y' => 96.15],  'description' => 'Port stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Uchqizil",     'coordinates' => ['lat' => 37.5500, 'lng' => 67.5000, 'x' => 50.63, 'y' => 82.9],   'description' => 'Uchqizil stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Naushaxar",    'coordinates' => ['lat' => 37.6500, 'lng' => 67.4500, 'x' => 45.38, 'y' => 80.87],  'description' => 'Naushaxar stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Sherabod",     'coordinates' => ['lat' => 37.7500, 'lng' => 67.4000, 'x' => 38.22, 'y' => 80.56],  'description' => 'Werobod stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Boldir",       'coordinates' => ['lat' => 38.0000, 'lng' => 67.7000, 'x' => 25.22, 'y' => 84.19],  'description' => 'Boldir stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Sement zavod", 'coordinates' => ['lat' => 37.8000, 'lng' => 66.9500, 'x' => 28.49, 'y' => 68.70],  'description' => 'Sement zavod stansiyasi' ],
            // [ 'type' => 'station', 'title' => "23 km",        'coordinates' => ['lat' => 38.1000, 'lng' => 67.0500, 'x' => 26.50, 'y' => 73.5],   'description' => '23 km stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Surxonobod",   'coordinates' => ['lat' => 38.3500, 'lng' => 68.0000, 'x' => 18.23, 'y' => 86.34],  'description' => 'Surxonobod stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "RZD",          'coordinates' => ['lat' => 38.4500, 'lng' => 68.1000, 'x' => 12.18, 'y' => 87.81],  'description' => 'RZD stansiyasi' ],
            [ 'type' => 'small_station', 'title' => "Kelif",        'coordinates' => ['lat' => 38.5500, 'lng' => 68.2000, 'x' => 6.49,  'y' => 92.32],  'description' => 'Kelif stansiyasi' ],

            [ 'type' => 'enterprise', 'title' => "PCH-15",  'coordinates' => ['lat' => 38.2731, 'lng' => 67.2086, 'x' => 35.96, 'y' => 24.68], 'description' => 'ПЧ-15 stansiyasi' ],
            [ 'type' => 'small_station',     'title' => "VP-4303", 'coordinates' => ['lat' => 38.2000, 'lng' => 67.2500, 'x' => 37.92, 'y' => 30.43], 'description' => 'ВП-4303 stansiyasi' ],
            [ 'type' => 'bridge',      'title' => "Toifali most", 'coordinates' => ['lat' => 38.1500, 'lng' => 67.4000, 'x' => 44.16, 'y' => 28.72], 'description' => 'МОСТ stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "ECHK-33", 'coordinates' => ['lat' => 38.1000, 'lng' => 67.5000, 'x' => 48.83, 'y' => 27.15], 'description' => 'ЭЧК-33 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "TO-2",    'coordinates' => ['lat' => 37.8500, 'lng' => 67.8000, 'x' => 63.48, 'y' => 39.54], 'description' => 'ТО-2 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "ECHK-34", 'coordinates' => ['lat' => 37.7500, 'lng' => 67.7500, 'x' => 60.91, 'y' => 44.55], 'description' => 'ЭЧК-34 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "ECHK-35", 'coordinates' => ['lat' => 37.6000, 'lng' => 67.7500, 'x' => 61.87, 'y' => 55.02], 'description' => 'ЭЧК-35 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "PMS-166", 'coordinates' => ['lat' => 38.2500, 'lng' => 67.9500, 'x' => 73.08, 'y' => 24.32], 'description' => 'ПМС-166 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "ECH-7",   'coordinates' => ['lat' => 37.3000, 'lng' => 67.4500, 'x' => 54.66, 'y' => 84.08], 'description' => 'ЭЧ-7 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "VCHD-16", 'coordinates' => ['lat' => 37.2833, 'lng' => 67.4667, 'x' => 56.02, 'y' => 85.13], 'description' => 'ВЧД-16 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "TCH-9",   'coordinates' => ['lat' => 37.2500, 'lng' => 67.5000, 'x' => 58.73, 'y' => 87.75], 'description' => 'ТЧ-9 stansiyasi' ],
            [ 'type' => 'small_station',     'title' => "VP-4302", 'coordinates' => ['lat' => 37.2833, 'lng' => 67.3833, 'x' => 49.72, 'y' => 85.59], 'description' => 'ВП-4302 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "PCH-13",  'coordinates' => ['lat' => 37.2667, 'lng' => 67.4000, 'x' => 51.66, 'y' => 86.73], 'description' => 'ПЧ-13 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "SHCH-9",  'coordinates' => ['lat' => 37.2500, 'lng' => 67.4167, 'x' => 53.62, 'y' => 88.11], 'description' => 'ШЧ-9 stansiyasi' ],
            [ 'type' => 'enterprise',  'title' => "TERMIZ-MTU",'coordinates' => ['lat' => 37.2333, 'lng' => 67.4333, 'x' => 55.00, 'y' => 88.69], 'description' => 'ТЕРМИЗ-МТУ stansiyasi' ],
        ];

        foreach ($stations as $station) {
            Station::create($station);
        }
    }
}