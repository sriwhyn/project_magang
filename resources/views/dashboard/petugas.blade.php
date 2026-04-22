@extends('layouts.app')
@section('title', 'Dashboard Petugas')

@push('styles')
<style>
    .petugas-header {
        background: var(--pnp-gradient);
        border-radius: 24px;
        color: white;
        padding: 42px;
        margin-bottom: 35px;
        position: relative; overflow: hidden;
        box-shadow: 0 10px 40px rgba(15, 23, 42, 0.2);
    }
    .petugas-header::after {
        content: ''; position: absolute; top: -50px; right: -50px; width: 250px; height: 250px;
        background: rgba(255,255,255,0.05); border-radius: 50%;
    }
    
    .avatar-pnp {
        width: 100px; height: 100px; border-radius: 50%; background: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 800; color: var(--pnp-navy);
        box-shadow: 0 0 0 8px rgba(255,255,255,0.1);
        flex-shrink: 0;
    }

    .stat-card-pnp {
        background: #ffffff; border-radius: 20px; padding: 24px; text-align: center;
        border: 1px solid var(--pnp-border); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%;
    }
    .stat-card-pnp:hover { transform: translateY(-6px); box-shadow: 0 15px 35px rgba(0,0,0,0.05); border-color: var(--pnp-blue); }
    
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px; font-size: 1.5rem;
    }
    
    .stat-val { font-size: 1.85rem; font-weight: 800; color: var(--pnp-navy); line-height: 1; margin-bottom: 4px; }
    .stat-lbl { font-size: 0.75rem; color: var(--pnp-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; }

    .table-pnp thead th {
        background: #f8fafc; border-bottom: 2px solid #f1f5f9;
        font-size: 0.72rem; font-weight: 800; color: var(--pnp-muted);
        text-transform: uppercase; letter-spacing: 1px; padding: 18px 24px;
    }
    .table-pnp tbody td { padding: 20px 24px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

    .status-badge-premium {
        font-size: 0.72rem; font-weight: 700; padding: 5px 14px; border-radius: 30px;
        display: inline-flex; align-items: center; gap: 7px; text-transform: uppercase;
    }
</style>
@endpush

@section('content')
    <div class="petugas-header d-flex flex-column flex-md-row align-items-md-center gap-4 animate-up">
        <div class="avatar-pnp">
            {{ strtoupper(substr(\Auth::user()->petugas->nama ?? \Auth::user()->email, 0, 1)) }}
        </div>
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h2 class="mb-0 fw-800">{{ \Auth::user()->petugas->nama ?? 'Petugas' }}</h2>
                <span class="badge bg-white text-primary border border-white rounded-pill px-3 shadow-sm fw-bold">{{ ucfirst(\Auth::user()->role) }}</span>
            </div>
            <p class="opacity-75 mb-3"><i class="bi bi-envelope-at me-2"></i>{{ \Auth::user()->email }}</p>
            
            <div class="d-flex flex-wrap align-items-center gap-4 mt-2 border-top border-white border-opacity-10 pt-3">
                <div>
                    <div class="opacity-50 small text-uppercase fw-800">Jabatan</div>
                    <div class="fw-bold fs-5">{{ \Auth::user()->petugas->jabatan ?? '-' }}</div>
                </div>
                <div>
                    <div class="opacity-50 small text-uppercase fw-800">Waky Tulis</div>
                    <div class="fw-bold fs-5">{{ \Auth::user()->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- // statistik --}}
    <div class="row g-4 mb-5">
        <div class="col-6 col-lg-3">
            <div class="stat-card-pnp shadow-sm">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-list-task"></i></div>
                <div class="stat-val">{{ $stats['total_tugas'] }}</div>
                <div class="stat-lbl">Total Tugas</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-pnp shadow-sm">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history"></i></div>
                <div class="stat-val">{{ $stats['tugas_pending'] }}</div>
                <div class="stat-lbl">Menunggu</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-pnp shadow-sm">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-gear"></i></div>
                <div class="stat-val">{{ $stats['tugas_aktif'] }}</div>
                <div class="stat-lbl">Diproses</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-pnp shadow-sm">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div class="stat-val">{{ $stats['tugas_selesai'] }}</div>
                <div class="stat-lbl">Selesai</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-5 p-0 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-800 mb-0"><i class="bi bi-briefcase text-primary me-2"></i>Antrian Pekerjaan Saya</h5>
            <a href="{{ route('petugas.kerusakan.index') }}" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm">Buka Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-pnp align-middle mb-0">
                <thead>
                    <tr>
                        <th>Deskripsi Laporan</th>
                        <th>Lokasi Fasilitas</th>
                        <th>Status Progress</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentKerusakan as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ Str::limit($item->judul_laporan, 50) }}</div>
                                <div class="text-muted small fw-600">{{ $item->created_at->format('d M Y') }}</div>
                            </td>
                            <td><i class="bi bi-geo-alt-fill me-2 text-primary"></i>{{ $item->lokasi_kerusakan }}</td>
                            <td>
                                @php
                                    $s = match($item->status_perbaikan) {
                                        'menunggu_petugas' => ['bg-light text-dark', 'Menunggu', 'bi-clock'],
                                        'dikerjakan' => ['bg-primary text-white', 'Diproses', 'bi-gear-wide-connected'],
                                        'menunggu_validasi_admin' => ['bg-warning text-dark', 'Dicek Admin', 'bi-shield-check'],
                                        'selesai' => ['bg-success text-white', 'Selesai', 'bi-check-all'],
                                        default => ['bg-light text-muted', 'Dilaporkan', 'bi-info-circle'],
                                    };
                                @endphp
                                <span class="status-badge-premium {{ $s[0] }} shadow-sm">
                                    <i class="bi {{ $s[2] }}"></i> {{ $s[1] }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('petugas.kerusakan.show', $item->id_laporan) }}" class="btn btn-sm btn-pnp-outline px-3 shadow-sm">Update Progress</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5 fw-600 small">Belum ada tugas perbaikan di antrian Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
