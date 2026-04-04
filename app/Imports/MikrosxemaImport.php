<?php

namespace App\Imports;

use App\Models\Mikrosxema;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MikrosxemaImport implements ToModel, WithHeadingRow, WithValidation
{
    protected int $stationId;

    public function __construct(int $stationId)
    {
        $this->stationId = $stationId;
    }

    public function model(array $row): ?Mikrosxema
    {
        return new Mikrosxema([
            'station_id' => $this->stationId,
            'nomi' => $row['nomi'],
            'texnik_holati' => $row['texnik_holati'],
            'biriktirilgan_joyi' => $row['biriktirilgan_joyi'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nomi' => 'required|string|max:255',
            'texnik_holati' => 'required|string|max:255',
            'biriktirilgan_joyi' => 'nullable|string|max:255',
        ];
    }
}
