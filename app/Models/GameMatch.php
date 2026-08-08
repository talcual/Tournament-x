<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Database\Factories\GameMatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class GameMatch extends Model
{
    /** @use HasFactory<GameMatchFactory> */
    use HasFactory;

    protected $table = 'game_matches';

    protected $fillable = [
        'tournament_id',
        'round_id',
        'group_id',
        'venue_id',
        'round_number',
        'match_number',
        'bracket_side',
        'bracket_index',
        'scheduled_at',
        'status',
        'notes',
        'next_match_id',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'match_number' => 'integer',
        'bracket_index' => 'integer',
        'scheduled_at' => 'datetime',
        'status' => MatchStatus::class,
    ];

    /**
     * @return BelongsTo<Tournament, $this>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return BelongsTo<Round, $this>
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /**
     * @return BelongsTo<TournamentGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }

    /**
     * @return BelongsTo<Venue, $this>
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * @return BelongsTo<GameMatch, $this>
     */
    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'next_match_id');
    }

    /**
     * @return HasMany<MatchParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class, 'match_id');
    }

    /**
     * @return MorphToMany<Team, $this>
     */
    public function teams(): MorphToMany
    {
        return $this->morphToMany(Team::class, 'participant', 'match_participants')
            ->withPivot('side', 'score', 'is_winner')
            ->withTimestamps();
    }
}
