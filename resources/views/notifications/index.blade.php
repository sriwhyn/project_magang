@extends('layouts.app')

@section('title', 'Notifikasi - Smart Campus')

@push('styles')
<style>
    .notif-card {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--pnp-border);
        transition: all 0.2s;
    }
    .notif-item {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
        text-decoration: none;
        display: block;
        position: relative;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: #f8fafc; }
    .notif-item.unread { background: rgba(10, 65, 116, 0.02); border-left: 4px solid var(--pnp-blue); }
    
    .notif-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    
    .unread-indicator {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--bs-danger);
        position: absolute; right: 24px; top: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 mb-1">Pusat Notifikasi</h2>
            <p class="text-muted mb-0 small">Lihat seluruh riwayat pemberitahuan sistem Anda di sini.</p>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
        <a href="{{ route('notifications.readAll') }}" class="btn btn-pnp-outline px-4">
            <i class="bi bi-check-all me-2"></i> Tandai Semua Dibaca
        </a>
        @endif
    </div>

    <div class="notif-card shadow-sm overflow-hidden">
        @forelse($notifications as $notif)
        <a href="{{ $notif->data['url'] ?? '#' }}" 
           onclick="markAsRead(event, '{{ $notif->id }}', '{{ $notif->data['url'] ?? '#' }}')"
           class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
            <div class="d-flex gap-4 align-items-start">
                <div class="notif-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold mb-0 {{ $notif->read_at ? 'text-secondary' : 'text-dark' }}">{{ $notif->data['judul'] }}</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mb-0 text-muted small" style="line-height: 1.6;">{{ $notif->data['pesan'] }}</p>
                </div>
            </div>
            @if(!$notif->read_at)
            <div class="unread-indicator"></div>
            @endif
        </a>
        @empty
        <div class="p-5 text-center">
            <div class="mb-3">
                <i class="bi bi-bell-slash text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h5 class="fw-bold text-muted">Belum ada notifikasi</h5>
            <p class="text-muted small">Semua pemberitahuan sistem akan muncul di sini.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-3">Kembali ke Dashboard</a>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
