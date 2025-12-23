<?php

namespace App;

enum LocomotiveType: string
{
    case ELECTRIC = 'Elektrovoz';
    case DISEL = 'Tiplovoz';
    case MANYOVR = 'MANYOVR';

    public function label(): string
    {
        return match ($this) {
            self::ELECTRIC => 'Elektrovoz',
            self::DISEL => 'Teplovoz',
            self::MANYOVR => 'Manyovr lokomotivi',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}