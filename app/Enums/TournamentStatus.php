<?php

namespace App\Enums;

enum TournamentStatus: string
{
    case Draft = 'draft';
    case Registration = 'registration';
    case Seeding = 'seeding';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return trans('app.status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Registration => 'blue',
            self::Seeding => 'yellow',
            self::InProgress => 'green',
            self::Completed => 'slate',
            self::Cancelled => 'red',
        };
    }
}
