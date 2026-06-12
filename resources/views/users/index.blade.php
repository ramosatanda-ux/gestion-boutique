@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">👤 Utilisateurs</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">➕ Ajouter</a>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">👥</div>
                <div>
                    <div class="text-muted small">Total utilisateurs</div>
                    <div class="fw-bold fs-4">{{ $users->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">🔑</div>
                <div>
                    <div class="text-muted small">Administrateurs</div>
                    <div class="fw-bold fs-4 text-danger">{{ $users->where('role', 'admin')->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">👤</div>
                <div>
                    <div class="text-muted small">Vendeurs</div>
                    <div class="fw-bold fs-4 text-primary">{{ $users->where('role', 'user')->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Utilisateur</th>
                        <th class="d-mobile-none">Email</th>
                        <th>Rôle</th>
                        <th class="d-mobile-none">Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                {{-- Avatar initiales --}}
                                <div style="
                                    width:36px; height:36px; border-radius:50%;
                                    background: {{ $user->role === 'admin' ? '#dc2626' : '#3b82f6' }};
                                    color:white; font-weight:700; font-size:.85rem;
                                    display:flex; align-items:center; justify-content:center;
                                    flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-secondary ms-1" style="font-size:.6rem;">Vous</span>
                                        @endif
                                    </div>
                                    <div class="text-muted d-md-none" style="font-size:.72rem;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="d-mobile-none">
                            <span class="text-muted small">{{ $user->email }}</span>
                        </td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">🔑 Admin</span>
                            @else
                                <span class="badge bg-primary">👤 Vendeur</span>
                            @endif
                        </td>
                        <td class="d-mobile-none">
                            <span class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="btn btn-warning btn-sm" title="Modifier">✏️</a>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                          onsubmit="return confirm('Supprimer « {{ $user->name }} » ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Supprimer">🗑️</button>
                                    </form>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled title="Votre compte">🔒</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Aucun utilisateur</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
