<?php

namespace App\Imports;

use App\Models\Avtomobil;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AvtomobilImport implements ToModel, WithHeadingRow, WithValidation
{
    protected int $stationId;

    public function __construct(int $stationId)
    {
        $this->stationId = $stationId;
    }

    public function model(array $row): ?Avtomobil
    {
        return new Avtomobil([
            'station_id' => $this->stationId,
            'rusumi' => $row['rusumi'],
            'davlat_raqami' => $row['davlat_raqami'],
            'ishlab_chiqarilgan_yili' => $row['ishlab_chiqarilgan_yili'],
            'biriktirilgan_shaxs' => $row['biriktirilgan_shaxs'],
        ]);
    }

    public function rules(): array
    {
        return [
            'rusumi' => 'required|string|max:255',
            'davlat_raqami' => 'required|string|max:255',
            'ishlab_chiqarilgan_yili' => 'required|numeric|min:1950|max:' . date('Y'),
            'biriktirilgan_shaxs' => 'required|string|max:255',
        ];
    }
}
