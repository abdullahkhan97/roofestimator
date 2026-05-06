<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Reset Spatie permission cache ─────────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 2. Define all permissions used by the app ─────────────────────
        $permissionNames = [
            'manage pricing',
            'manage users',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // ── 3. Create admin role with ALL permissions ─────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissionNames);

        // ── 4. Remove any stray roles that aren't admin ───────────────────
        //    (e.g. the accidental "user" role)
        Role::where('name', '!=', 'admin')->delete();

        // ── 5. Create the default admin user ─────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'role'     => 'admin',
            ]
        );

        // ── 6. Make sure admin ONLY has the admin role (clean slate) ──────
        $admin->syncRoles(['admin']);

        // ── 7. Fix any other users that have the now-deleted "user" role ──
        User::where('id', '!=', $admin->id)->each(function (User $user) {
            // Strip any roles that no longer exist, keep valid ones
            $validRoles = $user->roles->filter(
                fn($r) => Role::where('name', $r->name)->exists()
            )->pluck('name')->all();

            $user->syncRoles($validRoles);
        });

        $this->command->info('✓ Permissions created: ' . implode(', ', $permissionNames));
        $this->command->info('✓ Admin role created with all permissions');
        $this->command->info('✓ Admin user ready:');
        $this->command->info('    Email:    admin@example.com');
        $this->command->info('    Password: password');
    }
}
