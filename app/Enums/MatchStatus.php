<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Scheduled = 'scheduled';
    case Live = 'live';
    case Finished = 'finished';
    case Walkover = 'walkover';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return trans('app.match_status.'.$this->value);
    }
}
