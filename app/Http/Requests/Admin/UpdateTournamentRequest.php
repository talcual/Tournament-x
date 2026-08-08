<?php

namespace App\Http\Requests\Admin;

use App\Enums\ParticipantType;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update tournaments', $this->route('tournament')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tournamentId = $this->route('tournament')?->id ?? $this->route('tournament');

        return [
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'organizer_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', "unique:tournaments,slug,{$tournamentId}"],
            'description' => ['nullable', 'string', 'max:5000'],
            'format' => ['required', Rule::enum(TournamentFormat::class)],
            'status' => ['required', Rule::enum(TournamentStatus::class)],
            'participant_type' => ['required', Rule::enum(ParticipantType::class)],
            'max_participants' => ['nullable', 'integer', 'min:2', 'max:512'],
            'min_participants' => ['required', 'integer', 'min:2', 'max:64'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'registration_deadline' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'venues' => ['nullable', 'array'],
            'venues.*' => ['integer', 'exists:venues,id'],
        ];
    }
}
