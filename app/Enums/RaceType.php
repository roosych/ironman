<?php

declare(strict_types=1);

namespace App\Enums;

enum RaceType: string
{
    case Ironman = 'ironman';
    case Ironman703 = 'ironman_70_3';
    case Sprint5150 = '5150';

    public function label(): string
    {
        return match ($this) {
            self::Ironman => 'Ironman',
            self::Ironman703 => 'Ironman 70.3',
            self::Sprint5150 => '5150',
        };
    }
}
