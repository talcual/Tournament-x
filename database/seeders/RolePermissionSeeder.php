<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resources = ['tournaments', 'teams', 'players', 'venues', 'sports', 'users'];
        $actions = ['view', 'create', 'update', 'delete', 'manage'];

        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $name = "{$action} {$resource}";
                $permissions[] = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            }
        }

        $permissions[] = Permission::firstOrCreate(['name' => 'record match results', 'guard_name' => 'web']);
        $permissions[] = Permission::firstOrCreate(['name' => 'register tournaments', 'guard_name' => 'web']);

        $roles = [
            'super-admin' => $permissions,
            'admin' => collect($permissions)->reject(fn ($p) => $p->name === 'manage users')->values()->all(),
            'organizer' => [
                'view tournaments', 'create tournaments', 'update tournaments',
                'view teams', 'view players', 'view venues', 'view sports',
                'record match results',
            ],
            'referee' => [
                'view tournaments', 'view teams', 'view players',
                'record match results',
            ],
            'user' => [
                'view tournaments', 'view teams', 'view players',
                'view venues', 'view sports', 'register tournaments',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        $demoUsers = [
            ['admin@tournament-x.test', 'Site', 'Admin', 'admin'],
            ['organizer@tournament-x.test', 'Olivia', 'Organizer', 'organizer'],
            ['referee@tournament-x.test', 'Rex', 'Referee', 'referee'],
            ['user@tournament-x.test', 'Uma', 'User', 'user'],
        ];

        foreach ($demoUsers as [$email, $first, $last, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "{$first} {$last}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($role);
        }
    }
}
