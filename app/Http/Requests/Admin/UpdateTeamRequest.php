<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update teams') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $teamId = $this->route('team')?->id ?? $this->route('team');

        return [
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', "unique:teams,slug,{$teamId}"],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'home_venue' => ['nullable', 'string', 'max:160'],
            'coach_name' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['boolean'],
        ];
    }
}
