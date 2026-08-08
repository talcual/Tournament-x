<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Venue;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            ['label' => 'Tournaments', 'value' => Tournament::count(), 'icon' => '🏆'],
            ['label' => 'Active Tournaments', 'value' => Tournament::whereNotIn('status', ['draft', 'cancelled', 'completed'])->count(), 'icon' => '📈'],
            ['label' => 'Teams', 'value' => Team::count(), 'icon' => '👥'],
            ['label' => 'Players', 'value' => Player::count(), 'icon' => '⭐'],
            ['label' => 'Venues', 'value' => Venue::count(), 'icon' => '📍'],
            ['label' => 'Sports', 'value' => Sport::count(), 'icon' => '🎯'],
            ['label' => 'Users', 'value' => User::count(), 'icon' => '🛡️'],
        ];

        $upcomingTournaments = Tournament::with('sport')
            ->whereIn('status', ['registration', 'seeding'])
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'upcomingTournaments' => $upcomingTournaments,
        ]);
    }
}
