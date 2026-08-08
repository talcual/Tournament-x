<?php

use App\Enums\MatchStatus;

if (! function_exists('match_status_color')) {
    function match_status_color(MatchStatus $status): string
    {
        return match ($status) {
            MatchStatus::Scheduled => 'gray',
            MatchStatus::Live => 'green',
            MatchStatus::Finished => 'blue',
            MatchStatus::Walkover => 'yellow',
            MatchStatus::Cancelled => 'red',
        };
    }
}
