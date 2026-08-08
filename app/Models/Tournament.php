<?php

namespace App\Models;

use App\Enums\ParticipantType;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'organizer_id',
        'name',
        'slug',
        'description',
        'format',
        'status',
        'max_participants',
        'min_participants',
        'starts_at',
        'ends_at',
        'registration_deadline',
        'participant_type',
        'is_featured',
    ];

    protected $casts = [
        'format' => TournamentFormat::class,
        'status' => TournamentStatus::class,
        'participant_type' => ParticipantType::class,
        'starts_at' => 'date',
        'ends_at' => 'date',
        'registration_deadline' => 'datetime',
        'max_participants' => 'integer',
        'min_participants' => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * @return BelongsTo<Sport, $this>
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * @return BelongsToMany<Venue, $this>
     */
    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * @return HasMany<TournamentRegistration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    /**
     * @return MorphToMany<Team, $this>
     */
    public function teams(): MorphToMany
    {
        return $this->morphToMany(Team::class, 'participant', 'tournament_registrations', 'tournament_id', 'participant_id')
            ->withPivot('seed', 'is_confirmed')
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Player, $this>
     */
    public function players(): MorphToMany
    {
        return $this->morphToMany(Player::class, 'participant', 'tournament_registrations', 'tournament_id', 'participant_id')
            ->withPivot('seed', 'is_confirmed')
            ->withTimestamps();
    }

    /**
     * @return HasMany<TournamentGroup, $this>
     */
    public function tournamentGroups(): HasMany
    {
        return $this->hasMany(TournamentGroup::class);
    }

    /**
     * @return HasMany<Round, $this>
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    /**
     * @return HasMany<GameMatch, $this>
     */
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    /**
     * @return HasMany<Standing, $this>
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class);
    }
}
