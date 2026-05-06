@extends('layouts.app', [
    'title'     => $user->exists ? 'Edit User' : 'Add User',
    'pageTitle' => $user->exists ? 'Edit user' : 'Add user',
])

@section('content')
    <form class="grid" method="POST"
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <section class="span-12" style="margin-bottom:4px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h1>{{ $user->exists ? 'Edit ' . $user->name : 'Add new user' }}</h1>
                    <p class="muted">{{ $user->exists ? 'Update account details and role assignment.' : 'Create a new account and assign a role.' }}</p>
                </div>
                <a class="button secondary" href="{{ route('admin.users.index') }}">Cancel</a>
            </div>
        </section>

        {{-- ── Account details ── --}}
        <section class="span-12 panel">
            <h2>Account details</h2>

            <div class="field">
                <label for="name">Full name</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                       placeholder="e.g. Jane Smith">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}" required
                       placeholder="jane@example.com">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div style="border-top:1px solid var(--line);margin:20px 0;"></div>

            <div class="field">
                <label for="password">
                    Password
                    @if($user->exists)
                        <span class="muted" style="text-transform:none;letter-spacing:0;font-weight:400;">— leave blank to keep current</span>
                    @endif
                </label>
                <input id="password" name="password" type="password"
                       @required(!$user->exists)
                       placeholder="{{ $user->exists ? 'Leave blank to keep unchanged' : 'Min. 8 characters' }}">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       @required(!$user->exists)
                       placeholder="Repeat password">
            </div>
        </section>

        {{-- ── Role assignment ── --}}
        <aside class="span-12 panel">
            <h2>Role</h2>
            <p class="muted" style="margin-bottom:18px;font-size:12px;">
                Assign at least one role. Roles control what this user can access.
                <a href="{{ route('admin.roles.index') }}" style="color:var(--accent);text-decoration:none;">Manage roles →</a>
            </p>

            @if($roles->isEmpty())
                <p style="font-size:13px;color:var(--muted);background:var(--soft);border:1px solid var(--line);border-radius:6px;padding:12px 14px;">
                    No roles exist yet.
                    <a href="{{ route('admin.roles.index') }}" style="color:var(--accent);">Create one first →</a>
                </p>
            @else
                @foreach($roles as $role)
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--line);border-radius:7px;margin-bottom:8px;cursor:pointer;transition:border-color .15s,background .15s;"
                         onclick="this.querySelector('input').click()"
                         onmouseover="this.style.borderColor='var(--accent)';this.style.background='var(--accent-light)'"
                         onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='var(--line)';this.style.background='#fff'}"
                         id="row-{{ $loop->index }}">
                        <input id="role-{{ $loop->index }}"
                               name="roles[]"
                               type="checkbox"
                               value="{{ $role->name }}"
                               style="accent-color:var(--accent);width:16px;height:16px;flex-shrink:0;cursor:pointer;"
                               @checked(in_array($role->name, old('roles', $user->getRoleNames()->all())))
                               onclick="event.stopPropagation();"
                               onchange="syncRowStyle(this)">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--ink);text-transform:capitalize;">{{ $role->name }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $role->users_count ?? $role->users()->count() }} user(s) assigned</div>
                        </div>
                    </div>
                @endforeach
                @error('roles')
                    <div class="error" style="margin-top:6px;">{{ $message }}</div>
                @enderror
            @endif

            <div style="border-top:1px solid var(--line);margin-top:24px;padding-top:20px;">
                <button class="button blue" type="submit" style="width:100%;">
                    {{ $user->exists ? 'Save changes' : 'Create user' }}
                </button>
            </div>
        </aside>

    </form>

    <script>
        function syncRowStyle(checkbox) {
            const row = checkbox.closest('[id^="row-"]');
            if (!row) return;
            if (checkbox.checked) {
                row.style.borderColor = 'var(--accent)';
                row.style.background  = 'var(--accent-light)';
            } else {
                row.style.borderColor = 'var(--line)';
                row.style.background  = '#fff';
            }
        }
        // Apply initial state on page load
        document.querySelectorAll('input[name="roles[]"]').forEach(syncRowStyle);
    </script>
@endsection
