@extends('layouts.app', ['title' => 'Roles & Permissions', 'pageTitle' => 'User management'])

@section('content')
<div class="grid">

    {{-- ── Header ── --}}
    <section class="span-12" style="margin-bottom:4px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <h1>Roles &amp; permissions</h1>
                <p class="muted">Create roles, assign permissions to them, then assign roles to users.</p>
            </div>
            <a class="button secondary" href="{{ route('admin.users.index') }}">← Back to users</a>
        </div>
    </section>

    {{-- ── Flash ── --}}
    @if(session('status'))
        <div class="span-12 status">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="span-12" style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:7px;font-size:13px;font-weight:500;">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Create new role ── --}}
    <section class="span-4 panel">
        <h2>Create new role</h2>
        <p class="muted" style="margin-bottom:18px;font-size:12px;">
            Role names should be lowercase, e.g.
            <code style="background:var(--bg);padding:1px 5px;border-radius:3px;font-size:11px;">estimator</code>,
            <code style="background:var(--bg);padding:1px 5px;border-radius:3px;font-size:11px;">viewer</code>.
        </p>

        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="field">
                <label for="role-name">Role name</label>
                <input id="role-name" name="name"
                       value="{{ old('name') }}"
                       placeholder="e.g. estimator"
                       required autocomplete="off"
                       style="text-transform:lowercase;">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            @if($permissions->isNotEmpty())
                <div class="field">
                    <label>Permissions</label>
                    @foreach($permissions as $permission)
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--line);">
                            <input id="new-perm-{{ $loop->index }}"
                                   name="permissions[]"
                                   type="checkbox"
                                   value="{{ $permission->name }}"
                                   @checked(in_array($permission->name, old('permissions', [])))
                                   style="accent-color:var(--accent);width:15px;height:15px;flex-shrink:0;">
                            <label for="new-perm-{{ $loop->index }}"
                                   style="text-transform:none;letter-spacing:0;font-size:13px;font-weight:400;color:var(--ink);cursor:pointer;margin:0;">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <button class="button" type="submit" style="width:100%;margin-top:6px;">Create role</button>
        </form>
    </section>

    {{-- ── Existing roles ── --}}
    <section class="span-8">
        <div style="display:flex;flex-direction:column;gap:16px;">

            @forelse($roles as $role)
                <div class="panel" style="padding:20px 24px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:16px;">
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="display:inline-flex;align-items:center;background:var(--accent-light);color:var(--accent);border:1px solid #a7d9d4;border-radius:20px;padding:3px 14px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">
                                    {{ $role->name }}
                                </span>
                                <span style="font-size:12px;color:var(--muted);">
                                    {{ $role->users_count }} user{{ $role->users_count !== 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>

                        {{-- Delete (protected) --}}
                        @if(strtolower($role->name) === 'admin')
                            <span style="font-size:11px;color:var(--muted);font-style:italic;padding-top:4px;">Protected</span>
                        @elseif($role->users_count > 0)
                            <span title="Reassign users before deleting"
                                  style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:1px solid var(--line);background:var(--soft);color:var(--line);cursor:not-allowed;flex-shrink:0;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                  onsubmit="return confirm('Delete role &quot;{{ $role->name }}&quot;? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete role"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;cursor:pointer;flex-shrink:0;"
                                        onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                        onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Permissions for this role ──────────────────────── --}}
                    @if($permissions->isEmpty())
                        <p style="font-size:12px;color:var(--muted);">No permissions defined in the system.</p>
                    @else
                        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                            @csrf @method('PUT')
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:6px;margin-bottom:14px;">
                                @foreach($permissions as $permission)
                                    <label style="display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid var(--line);border-radius:6px;cursor:pointer;font-size:12px;font-weight:400;color:var(--ink);text-transform:none;letter-spacing:0;transition:border-color .15s,background .15s;"
                                           onmouseover="this.style.borderColor='var(--accent)';this.style.background='var(--accent-light)'"
                                           onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='var(--line)';this.style.background='#fff'}"
                                           id="lbl-{{ $role->id }}-{{ $loop->index }}">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               @checked($role->permissions->contains('name', $permission->name))
                                               style="accent-color:var(--accent);width:14px;height:14px;flex-shrink:0;"
                                               onchange="syncLabel(this)">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                            <button class="button secondary" type="submit"
                                    style="min-height:34px;font-size:11px;padding:0 14px;">
                                Save permissions
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="panel" style="padding:48px 24px;text-align:center;">
                    <p class="muted">No roles yet. Create your first one.</p>
                </div>
            @endforelse

        </div>
    </section>

</div>

<script>
function syncLabel(checkbox) {
    const label = checkbox.closest('label');
    if (!label) return;
    if (checkbox.checked) {
        label.style.borderColor = 'var(--accent)';
        label.style.background  = 'var(--accent-light)';
    } else {
        label.style.borderColor = 'var(--line)';
        label.style.background  = '#fff';
    }
}
// Apply initial checked state styling on load
document.querySelectorAll('input[name="permissions[]"]').forEach(syncLabel);
</script>
@endsection
