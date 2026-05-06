<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        $roles       = Role::with('permissions')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name'       => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('status', 'Role "' . $role->name . '" created.');
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        $validated = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('status', 'Permissions for "' . $role->name . '" updated.');
    }

    public function destroy(Request $request, Role $role)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        if (strtolower($role->name) === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'The admin role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete "' . $role->name . '" — ' . $role->users()->count() . ' user(s) still assigned. Reassign them first.');
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('status', 'Role "' . $name . '" deleted.');
    }
}
