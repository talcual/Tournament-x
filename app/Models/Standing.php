<?php

namespace App\Models;

use Database\Factories\StandingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Standing extends Model
{
    /** @use HasFactory<StandingFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'group_id',
        'participant_id',
        'participant_type',
        'played',
        'wins',
        'draws',
        'losses',
        'goals_for',
        'goals_against',
        'points',
        'position',
    ];

    protected $casts = [
        'played' => 'integer',
        'wins' => 'integer',
        'draws' => 'integer',
        'losses' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'points' => 'integer',
        'position' => 'integer',
    ];

    public function goalDifference(): int
    {
        return $this->goals_for - $this->goals_against;
    }

    /**
     * @return BelongsTo<Tournament, $this>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return BelongsTo<TournamentGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function participant(): MorphTo
    {
        return $this->morphTo();
    }
}
