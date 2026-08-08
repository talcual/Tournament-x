@extends('layouts.admin', ['title' => 'Edit User'])

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-2xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf @method('PUT')
        <x-form-input name="name" label="Name" :value="$user->name" :required="true" />
        <x-form-input name="email" label="Email" type="email" :value="$user->email" :required="true" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="password" label="New password (leave blank to keep)" type="password" />
            <x-form-input name="password_confirmation" label="Confirm new password" type="password" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Roles</label>
            <div class="mt-2 grid grid-cols-2 gap-2">
                @foreach($roles as $role)
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked($user->hasRole($role->name)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        {{ ucfirst($role->name) }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save changes</button>
        </div>
    </form>
@endsection