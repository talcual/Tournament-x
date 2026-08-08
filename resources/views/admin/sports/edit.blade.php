@extends('layouts.admin', ['title' => __('app.admin.actions.edit').' · '.__('app.admin.sports')])

@section('content')
    <form method="POST" action="{{ route('admin.sports.update', $sport) }}" class="max-w-2xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf @method('PUT')
        <x-form-input name="name" :label="__('app.fields.name')" :value="$sport->name" :required="true" />
        <x-form-input name="slug" :label="__('app.fields.slug')" :value="$sport->slug" />
        <x-form-input name="icon" :label="__('app.fields.icon')" :value="$sport->icon" />
        <x-form-textarea name="description" :label="__('app.fields.description')" :value="$sport->description" :rows="3" />
        <x-form-checkbox name="is_team_sport" :label="__('app.fields.is_team_sport')" :value="$sport->is_team_sport" />

        <div class="border-t pt-5">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('app.sport.scoring_rules') }}</h3>
            <div class="mt-3 grid grid-cols-3 gap-4">
                <x-form-input name="points_per_win" :label="__('app.sport.points_per_win')" type="number" :value="$sport->points_per_win" :required="true" />
                <x-form-input name="points_per_draw" :label="__('app.sport.points_per_draw')" type="number" :value="$sport->points_per_draw" :required="true" />
                <x-form-input name="points_per_loss" :label="__('app.sport.points_per_loss')" type="number" :value="$sport->points_per_loss" :required="true" />
            </div>
            <div class="mt-4">
                <x-form-checkbox name="allows_draws" :label="__('app.sport.allows_draws')" :value="$sport->allows_draws" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.sports.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">{{ __('app.admin.actions.cancel') }}</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">{{ __('app.admin.actions.update') }}</button>
        </div>
    </form>
@endsection