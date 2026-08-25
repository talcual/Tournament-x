<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MatchResultController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\TournamentController as PublicTournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicTournamentController::class, 'index'])->name('home');

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tournaments/create', [App\Http\Controllers\Admin\TournamentController::class, 'userCreate'])->name('user.tournaments.create');
    Route::post('/tournaments', [App\Http\Controllers\Admin\TournamentController::class, 'userStore'])->name('user.tournaments.store');

    Route::get('/dashboard', function () {
        if (auth()->user()->hasAnyRole(['admin', 'organizer', 'referee'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');
});

Route::get('/tournaments/{tournament:slug}', [PublicTournamentController::class, 'show'])->name('public.tournaments.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->middleware(['auth', 'role:super-admin|admin|organizer|referee'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::middleware('role:super-admin|admin')->group(function () {
            Route::resource('sports', SportController::class)->except(['show']);
            Route::get('sports/{sport}', [SportController::class, 'show'])->name('sports.show');
        });

        Route::resource('tournaments', TournamentController::class);

        Route::prefix('tournaments/{tournament}')->name('tournaments.')->group(function () {
            Route::get('draw', [TournamentController::class, 'draw'])->name('draw');
            Route::post('draw', [TournamentController::class, 'generateDraw'])->name('draw.generate');
            Route::get('matches', [TournamentController::class, 'matches'])->name('matches');
            Route::get('standings', [TournamentController::class, 'standings'])->name('standings');
            Route::get('matches/{match}/finish', [MatchResultController::class, 'edit'])->name('matches.finish');
            Route::post('matches/{match}/finish', [MatchResultController::class, 'update'])->name('matches.finish.store');
        });

        Route::resource('teams', TeamController::class);
        Route::resource('players', PlayerController::class);

        Route::middleware('role:super-admin|admin')->group(function () {
            Route::resource('venues', VenueController::class);
        });

        Route::middleware('role:super-admin')->group(function () {
            Route::resource('users', UserController::class)->except(['show', 'create']);
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        });
    });

require __DIR__.'/auth.php';
