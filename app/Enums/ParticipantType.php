<?php

namespace App\Enums;

enum ParticipantType: string
{
    case Team = 'team';
    case Player = 'player';

    public function label(): string
    {
        return trans('app.participant_type.'.$this->value);
    }
}
