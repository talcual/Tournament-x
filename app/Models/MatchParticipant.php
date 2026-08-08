<?php

namespace App\Models;

use Database\Factories\MatchParticipantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MatchParticipant extends Model
{
    /** @use HasFactory<MatchParticipantFactory> */
    use HasFactory;

    protected $fillable = [
        'match_id',
        'participant_id',
        'participant_type',
        'side',
        'score',
        'is_winner',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_winner' => 'boolean',
    ];

    /**
     * @return BelongsTo<GameMatch, $this>
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function participant(): MorphTo
    {
        return $this->morphTo();
    }
}
