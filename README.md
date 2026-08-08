# TournamentX

A multi-sport, multi-format tournament management system built with **Laravel 13**, **Blade**, **Tailwind CSS**, and **Spatie Laravel Permission**.

Features a full draw/bracket engine with per-sport scoring rules, a result recorder with auto-propagation to the next round, and bilingual UI (Spanish + English).

---

## Features

- **Bilingual UI** (Spanish default, English available) with a locale switcher in the header
- **Authentication** powered by Laravel Breeze (Blade preset)
- **Role-based access control** via `spatie/laravel-permission` with 5 seeded roles: `super-admin`, `admin`, `organizer`, `referee`, `user`
- **Per-sport scoring rules** — each sport stores `points_per_win`, `points_per_draw`, `points_per_loss`, `allows_draws`. Example: Football 3/1/0 (with draws), Volleyball 1/0/0 (no draws).
- **Domain model** for multi-sport tournaments:
  - Sports, Venues, Teams, Players (individual or team participants)
  - Tournaments with 5 formats (`single_elimination`, `double_elimination`, `round_robin`, `swiss`, `groups_knockout`) and 6 statuses
  - Groups, Rounds, Matches, Match Participants (polymorphic), Standings
- **Bracket generator** (`App\Services\BracketGenerator`) — implements single elimination (with byes for non-power-of-two), double elimination (Winners + Losers + Grand Final), round robin (circle method), groups + knockout. Swiss is not yet supported.
- **Result recorder** (`App\Services\FinishMatch`) — accepts score per participant, winner (or draw if sport allows), updates `match_participants`, propagates the winner to the next round, and updates standings using the sport's scoring rules.
- **Admin panel** at `/admin` with CRUD for every resource + Draw/Matches/Standings per tournament
- **Public site** at `/` with live matches and standings
- **79 PHPUnit tests** covering authorization, CRUD, i18n, bracket generator, and finish-match service
- **Pint** configured for code style

## Tech Stack

| Layer        | Choice                                                    |
| ------------ | --------------------------------------------------------- |
| Framework    | Laravel 13 (PHP 8.3+)                                     |
| Frontend     | Blade, Tailwind CSS 4, Alpine.js                          |
| Auth         | Laravel Breeze (Blade preset)                             |
| Permissions  | `spatie/laravel-permission`                               |
| Database     | MySQL 8 (production) · SQLite in-memory (tests)           |
| Tests        | PHPUnit 12                                                |
| Linting      | Laravel Pint                                              |
| i18n         | Laravel `lang/` files (ES default, EN available)          |

## Requirements

- PHP 8.3+
- Composer 2
- Node.js 18+ & npm
- MySQL 8 (or SQLite for local dev)

## Installation

```bash
# 1. Clone and install PHP dependencies
git clone <repo-url> tournament-x
cd tournament-x
composer install

# 2. Install and build frontend assets
npm install
npm run build

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure your DB in .env, then migrate & seed
php artisan migrate --seed

# 5. Serve
php artisan serve
```

Visit `http://127.0.0.1:8000`.

## Switching Language

The application defaults to Spanish. A language selector is available in both the public header and the admin sidebar. Locale is persisted in session and in a `locale` cookie.

Available locales: `en`, `es`. Set `APP_LOCALE` in `.env` to change the default.

## Demo Accounts

After `php artisan db:seed`, these accounts exist (password is `password` for all):

| Role         | Email                            |
| ------------ | -------------------------------- |
| Super Admin  | `admin@tournament-x.test`        |
| Organizer    | `organizer@tournament-x.test`    |
| Referee      | `referee@tournament-x.test`      |
| User         | `user@tournament-x.test`         |

## How to Run a Tournament (organizer flow)

1. Log in as admin or organizer, go to `/admin/tournaments/{id}/edit`, ensure the tournament has the right sport, format, and participants.
2. Open `/admin/tournaments/{id}/draw` and click **Generar cuadro / Generate bracket**. This:
   - Wipes existing matches, rounds, groups, and standings.
   - Creates the bracket according to format.
   - Seeds round-robin with the circle method.
   - Creates bye rounds for non-power-of-two in single elimination.
   - For double elimination creates Winners, Losers, and Grand Final rounds.
3. Open `/admin/tournaments/{id}/matches` to see the schedule, then click **Finalizar encuentro / Finish match** on a scheduled match.
4. Pick a winner (or **Empate / Draw** if the sport allows), enter the score for each side, save. Standings update automatically based on the sport's scoring rules.
5. Standings live at `/admin/tournaments/{id}/standings`. Public viewers see matches + standings at `/tournaments/{slug}`.

## Project Structure

```
app/
├── Enums/                    # TournamentFormat, TournamentStatus, MatchStatus, ParticipantType
├── Http/
│   ├── Controllers/
│   │   ├── Admin/            # Dashboard, CRUD resource controllers, MatchResultController
│   │   ├── LocaleController
│   │   └── Public/           # PublicTournamentController
│   ├── Middleware/           # EnsureUserHasRole, SetLocale
│   └── Requests/Admin/       # Store/Update form requests per resource
├── Models/
├── Policies/
└── Services/                 # BracketGenerator, FinishMatch
database/
├── migrations/               # Including spatie/permission + scoring columns
├── factories/
└── seeders/                  # RolePermissionSeeder + DatabaseSeeder
lang/
├── en/                       # English strings
└── es/                       # Spanish strings
resources/views/              # admin/, public/, layouts/, components/
tests/                        # Feature/, Unit/Services/, Unit/Models/
```

## Roles & Permissions

Defined in `database/seeders/RolePermissionSeeder.php`:

- **`super-admin`** — full access including user management
- **`admin`** — manages all resources except user accounts
- **`organizer`** — manages own tournaments, can record match results
- **`referee`** — views tournaments and records match results
- **`user`** — public views only

## Testing

```bash
php artisan test
```

Runs the **79 tests / 193 assertions** suite using an in-memory SQLite database.

## Code Style

```bash
vendor/bin/pint          # auto-fix
vendor/bin/pint --test   # check only (CI mode)
```

## What's NOT included (planned next iterations)

- Swiss-system draw generation (only listed as a format)
- Real-time broadcasting (Reverb)
- PDF / Excel reports
- Public API with Sanctum
- Notifications (email / in-app)
- Multi-language validation messages (validation.php is still in English; Breeze default)

## License

MIT.