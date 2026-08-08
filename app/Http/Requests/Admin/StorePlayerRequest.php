<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create players') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:players,slug'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:80'],
            'ranking' => ['nullable', 'integer', 'min:1'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:3500'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
