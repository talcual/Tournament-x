<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('super-admin'), 403);

        $query = User::with('roles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        return view('admin.users.create', [
            'user' => new User,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles'], $data['password_confirmation']);

        $user = User::create($data);
        if ($roles) {
            $user->syncRoles($roles);
        }

        return redirect()->route('admin.users.index')->with('status', "User '{$user->name}' created.");
    }

    public function edit(User $user): View
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles'], $data['password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')->with('status', "User '{$user->name}' updated.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        if ($user->id === auth()->id()) {
            return back()->withErrors(['status' => 'You cannot delete yourself.']);
        }
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "User '{$name}' deleted.");
    }
}
