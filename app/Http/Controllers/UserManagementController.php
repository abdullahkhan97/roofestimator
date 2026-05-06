<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        return view('admin.users.form', [
            'user'  => new User(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'roles'                 => ['required', 'array', 'min:1'],
            'roles.*'               => ['exists:roles,name'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['roles'][0],
            'is_admin' => in_array('admin', $validated['roles'], true),
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('status', 'User "' . $user->name . '" created successfully.');
    }

    public function edit(Request $request, User $user)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        return view('admin.users.form', [
            'user'  => $user->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()?->can('manage users'), 403);

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'roles'                 => ['required', 'array', 'min:1'],
            'roles.*'               => ['exists:roles,name'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable'],
        ]);

        $user->fill([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['roles'][0],
            'is_admin' => in_array('admin', $validated['roles'], true),
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('status', 'User "' . $user->name . '" updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()?->can('manage users'), 403);
        abort_if($request->user()->is($user), 422, 'You cannot delete your own account.');

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'User "' . $name . '" has been deleted.');
    }
}
