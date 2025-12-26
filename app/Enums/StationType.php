<?php

namespace App\Enums;

enum StationType: string
{
    case SMALL_STATION = 'small_station';
    case BIG_STATION   = 'big_station';

    case ENTERPRISE_PCH = 'enterprise_pch';
    case ENTERPRISE_TCH = 'enterprise_tch';
    case ENTERPRISE_SHCH = 'enterprise_shch';
    case ENTERPRISE_ECH = 'enterprise_ech';
    case ENTERPRISE_TO = 'enterprise_to';
    case ENTERPRISE_PMS = 'enterprise_pms';
    case ENTERPRISE_VCHD = 'enterprise_vchd';
    case ENTERPRISE_RJU = 'enterprise_rju';

    case BRIDGE = 'bridge';

    public function label(): string
    {
        return match ($this) {
            self::SMALL_STATION => 'Kichik stansiya',
            self::BIG_STATION   => 'Katta stansiya',

            self::ENTERPRISE_PCH => 'Korxona (PCH)',
            self::ENTERPRISE_TCH => 'Korxona (TCH)',
            self::ENTERPRISE_SHCH => 'Korxona (SHCH)',
            self::ENTERPRISE_ECH => 'Korxona (ECH)',
            self::ENTERPRISE_TO => 'Korxona (TO)',
            self::ENTERPRISE_PMS => 'Korxona (PM)',
            self::ENTERPRISE_VCHD => 'Korxona (VCH)',
            self::ENTERPRISE_RJU => 'Korxona (RJU)',

            self::BRIDGE => 'Ko‘prik',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function groupLabel(): string
    {
        return match ($this) {
            self::SMALL_STATION,
            self::BIG_STATION => 'Stansiya',

            self::ENTERPRISE_PCH,
            self::ENTERPRISE_TCH,
            self::ENTERPRISE_SHCH,
            self::ENTERPRISE_ECH,
            self::ENTERPRISE_TO,
            self::ENTERPRISE_PMS,
            self::ENTERPRISE_VCHD,
            self::ENTERPRISE_RJU => 'Korxona',

            self::BRIDGE => 'Ko‘prik',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BIG_STATION   => 'primary',
            self::SMALL_STATION => 'info',

            self::ENTERPRISE_PCH,
            self::ENTERPRISE_TCH,
            self::ENTERPRISE_SHCH,
            self::ENTERPRISE_ECH,
            self::ENTERPRISE_TO,
            self::ENTERPRISE_PMS,
            self::ENTERPRISE_VCHD,
            self::ENTERPRISE_RJU => 'success',

            self::BRIDGE => 'warning',
        };
    }

    public function isEnterprise(): bool
    {
        return str_starts_with($this->value, 'enterprise_');
    }
}