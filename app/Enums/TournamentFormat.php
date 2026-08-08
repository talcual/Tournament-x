<?php

namespace App\Enums;

enum TournamentFormat: string
{
    case SingleElimination = 'single_elimination';
    case DoubleElimination = 'double_elimination';
    case RoundRobin = 'round_robin';
    case Swiss = 'swiss';
    case GroupsKnockout = 'groups_knockout';

    public function label(): string
    {
        return trans('app.format.'.$this->value);
    }
}
