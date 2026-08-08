<?php

namespace App\Models;

use Database\Factories\SportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    /** @use HasFactory<SportFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_team_sport',
        'points_per_win',
        'points_per_draw',
        'points_per_loss',
        'allows_draws',
    ];

    protected $casts = [
        'is_team_sport' => 'boolean',
        'allows_draws' => 'boolean',
        'points_per_win' => 'integer',
        'points_per_draw' => 'integer',
        'points_per_loss' => 'integer',
    ];

    /**
     * @return HasMany<Team, $this>
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @return HasMany<Player, $this>
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * @return HasMany<Tournament, $this>
     */
    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }
}
