<?php

namespace App\Models;

use Database\Factories\TournamentRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TournamentRegistration extends Model
{
    /** @use HasFactory<TournamentRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'participant_id',
        'participant_type',
        'seed',
        'is_confirmed',
    ];

    protected $casts = [
        'seed' => 'integer',
        'is_confirmed' => 'boolean',
    ];

    /**
     * @return BelongsTo<Tournament, $this>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function participant(): MorphTo
    {
        return $this->morphTo();
    }
}
