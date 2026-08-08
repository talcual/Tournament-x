<?php

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'team_id',
        'first_name',
        'last_name',
        'slug',
        'birth_date',
        'nationality',
        'ranking',
        'rating',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'ranking' => 'integer',
        'rating' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * @return BelongsTo<Sport, $this>
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return MorphMany<TournamentRegistration, $this>
     */
    public function tournamentRegistrations(): MorphMany
    {
        return $this->morphMany(TournamentRegistration::class, 'participant');
    }
}
