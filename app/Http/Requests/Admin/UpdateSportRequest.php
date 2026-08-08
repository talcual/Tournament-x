<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update sports') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $sportId = $this->route('sport')?->id ?? $this->route('sport');

        return [
            'name' => ['required', 'string', 'max:120', "unique:sports,name,{$sportId}"],
            'slug' => ['nullable', 'string', 'max:140', "unique:sports,slug,{$sportId}"],
            'icon' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_team_sport' => ['boolean'],
            'points_per_win' => ['required', 'integer', 'min:0', 'max:100'],
            'points_per_draw' => ['required', 'integer', 'min:0', 'max:100'],
            'points_per_loss' => ['required', 'integer', 'min:0', 'max:100'],
            'allows_draws' => ['boolean'],
        ];
    }
}
