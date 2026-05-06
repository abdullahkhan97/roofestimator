@extends('layouts.app', ['title' => 'User Management', 'pageTitle' => 'User management'])

@section('content')
    <div class="grid">

        {{-- ── Header ── --}}
        <section class="span-12" style="margin-bottom:4px;">
            <div class="actions" style="justify-content:space-between;align-items:flex-start;">
                <div>
                    <h1>Users</h1>
                    <p class="muted">Manage accounts and assign roles to control access.</p>
                </div>
                <div class="actions">
                    <a class="button secondary" href="{{ route('admin.roles.index') }}">Manage roles</a>
                    <a class="button" href="{{ route('admin.users.create') }}">+ Add user</a>
                </div>
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

        {{-- ── Users table ── --}}
        <section class="span-12 panel" style="padding:0;overflow:hidden;">

            <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 26px;border-bottom:1px solid var(--line);">
                <h2 style="margin:0;padding:0;border:0;">
                    All accounts
                    <span style="margin-left:8px;background:var(--bg);border:1px solid var(--line);border-radius:20px;padding:2px 10px;font-size:10px;">
                        {{ $users->total() }}
                    </span>
                </h2>
            </div>

            @if($users->isEmpty())
                <div style="padding:56px 28px;text-align:center;">
                    <p class="muted" style="margin-bottom:16px;">No users yet.</p>
                    <a class="button" href="{{ route('admin.users.create') }}">Add first user</a>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left:26px;">Name</th>
                                <th>Email</th>
                                <th>Role(s)</th>
                                <th>Joined</th>
                                <th style="padding-right:26px;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td style="padding-left:26px;">
                                    <div style="font-weight:600;color:var(--ink);">{{ $user->name }}</div>
                                    @if($user->is(auth()->user()))
                                        <div style="font-size:11px;color:var(--accent);font-weight:600;letter-spacing:.04em;margin-top:2px;">You</div>
                                    @endif
                                </td>
                                <td style="color:var(--muted);">{{ $user->email }}</td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span style="display:inline-flex;align-items:center;background:var(--accent-light);color:var(--accent);border:1px solid #a7d9d4;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;margin-right:4px;">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span style="font-size:12px;color:var(--muted);">No role</span>
                                    @endforelse
                                </td>
                                <td style="color:var(--muted);font-size:12px;white-space:nowrap;">
                                    {{ $user->created_at?->format('M j, Y') }}
                                </td>
                                <td style="padding-right:26px;text-align:right;white-space:nowrap;">
                                    <div style="display:inline-flex;gap:6px;align-items:center;">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}" title="Edit user"
                                           style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;text-decoration:none;"
                                           onmouseover="this.style.borderColor='var(--accent-2)';this.style.color='var(--accent-2)'"
                                           onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>

                                        {{-- Delete (disabled for self) --}}
                                        @if($user->is(auth()->user()))
                                            <span title="Cannot delete your own account"
                                                  style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:var(--soft);color:var(--line);cursor:not-allowed;">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Delete user {{ addslashes($user->name) }}?\n\nThis cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete user"
                                                        style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;cursor:pointer;"
                                                        onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                                        onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div style="padding:14px 26px;border-top:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                        <span style="font-size:12px;color:var(--muted);">
                            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
                        </span>
                        <div style="display:flex;gap:5px;">
                            @if($users->onFirstPage())
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--muted);opacity:.35;font-size:15px;">‹</span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--ink);text-decoration:none;font-size:15px;">‹</a>
                            @endif
                            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                @if($page == $users->currentPage())
                                    <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--accent);background:var(--accent);color:#fff;font-size:12px;font-weight:600;">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--ink);font-size:12px;text-decoration:none;">{{ $page }}</a>
                                @endif
                            @endforeach
                            @if($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--ink);text-decoration:none;font-size:15px;">›</a>
                            @else
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--muted);opacity:.35;font-size:15px;">›</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

        </section>
    </div>
@endsection
