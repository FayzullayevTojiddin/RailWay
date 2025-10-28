<?php

namespace Database\Factories;

use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

class StationFactory extends Factory
{
    protected $model = Station::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['station', 'terminal', 'junction']),
            'title' => $this->faker->randomElement([
                'Termez', 'Qarshi', 'Denov', 'Shoʻrchi', 'Gʻuzor', 
                'Jarqoʻrgʻon', 'Hairaton', 'Boysun', 'Kumqoʻrgʻon'
            ]),
            'coordinates' => [
                'lat' => $this->faker->latitude(37, 39),
                'lng' => $this->faker->longitude(65, 68)
            ],
            'description' => $this->faker->sentence(12),
            'details' => [
                'platforms' => $this->faker->numberBetween(2, 8),
                'tracks' => $this->faker->numberBetween(2, 6),
                'electrified' => $this->faker->boolean(70),
                'facilities' => $this->faker->randomElements([
                    'Kutish zali', 
                    'Kassa', 
                    'Bufet', 
                    'WC', 
                    'Bagaj xonasi',
                    'Axborot taxtasi'
                ], $this->faker->numberBetween(2, 5))
            ],
            'images' => [
                'https://picsum.photos/800/600?random=' . $this->faker->numberBetween(1, 100),
                'https://picsum.photos/800/600?random=' . $this->faker->numberBetween(101, 200),
                'https://picsum.photos/800/600?random=' . $this->faker->numberBetween(201, 300),
            ]
        ];
    }
}