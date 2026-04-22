@extends('layouts.app')
@section('title', 'Admin Panel - Lost and Found')

@push('styles')
<style>
    .welcome-banner {
        background: var(--pnp-gradient);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(30, 58, 95, 0.15);
        position: relative; overflow: hidden;
    }
    .welcome-banner::after {
        content: ""; position: absolute; right: -50px; top: -50px; width: 200px; height: 200px;
        background: rgba(255,255,255,0.05); border-radius: 50%;
    }

    /* Premium Stat Cards */
    .stat-card-pnp {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--pnp-border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex; align-items: center; gap: 20px;
    }
    .stat-card-pnp:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.05); border-color: var(--pnp-blue); }
    
    .icon-box {
        width: 64px; height: 64px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; flex-shrink: 0;
    }
    .val-text { font-size: 2rem; font-weight: 800; color: var(--pnp-navy); line-height: 1; margin-bottom: 2px; }
    .label-text { font-size: 0.8rem; font-weight: 700; color: var(--pnp-muted); text-transform: uppercase; letter-spacing: 0.5px; }

    /* Custom Table Style */
    .table-pnp thead th {
        background: #f8fafc;
        border-bottom: 2px solid #f1f5f9;
        font-size: 0.72rem; font-weight: 800; color: var(--pnp-muted);
        text-transform: uppercase; letter-spacing: 0.8px;
        padding: 16px 24px;
    }
    .table-pnp tbody td { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="welcome-banner animate-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="fw-800 mb-2">Ringkasan Sistem</h1>
            <p class="opacity-75 mb-0 fs-5">Selamat datang, Administrator. Pantau seluruh aktivitas Lost & Found hari ini.</p>
        </div>
        <div class="col-lg-4 text-lg-end d-none d-lg-block">
            <i class="bi bi-shield-check" style="font-size: 5rem; opacity: 0.2;"></i>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('petugas.kehilangan.index') }}" class="text-decoration-none">
            <div class="stat-card-pnp shadow-sm">
                <div class="icon-box" style="background: rgba(10, 65, 116, 0.1); color: #0A4174;"><i class="bi bi-search"></i></div>
                <div>
                    <div class="val-text">{{ $stats['kehilangan'] }}</div>
                    <div class="label-text">Barang Hilang</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('petugas.temuan.index') }}" class="text-decoration-none">
            <div class="stat-card-pnp shadow-sm">
                <div class="icon-box" style="background: rgba(10, 65, 116, 0.1); color: #0A4174;"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="val-text">{{ $stats['temuan'] }}</div>
                    <div class="label-text">Barang Temuan</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-12">
        <a href="{{ route('petugas.kerusakan.index') }}" class="text-decoration-none">
            <div class="stat-card-pnp shadow-sm">
                <div class="icon-box" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="bi bi-tools"></i></div>
                <div>
                    <div class="val-text">{{ $stats['kerusakan'] }}</div>
                    <div class="label-text">Fasilitas Rusak</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="manajemen-card mb-5 bg-white shadow-sm" style="border-radius: 20px; border: 1px solid var(--pnp-border); overflow: hidden;">
    <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Antrian Verifikasi Klaim</h5>
        <a href="{{ route('petugas.klaim.index') }}" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-pnp align-middle mb-0">
            <thead>
                <tr>
                    <th>Informasi Barang</th>
                    <th>Nama Pengaju</th>
                    <th>Skor Kecocokan</th>
                    <th>Tanggal Masuk</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentKlaim as $item)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->barangTemuan->nama_barang ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ $item->barangTemuan->kategori->nama_kategori ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="small fw-600">{{ $item->pengaju->mahasiswa?->nama ?? $item->pengaju->email ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2" style="width: 150px;">
                            <div class="progress w-100" style="height: 6px; border-radius: 10px; background: #f1f5f9;">
                                <div class="progress-bar bg-{{ ($item->skor_kecocokan ?? 0) >= 70 ? 'success' : (($item->skor_kecocokan ?? 0) >= 40 ? 'warning' : 'danger') }}" style="width:{{ $item->skor_kecocokan ?? 0 }}%"></div>
                            </div>
                            <span class="fw-bold" style="font-size: 0.75rem;">{{ $item->skor_kecocokan ?? 0 }}%</span>
                        </div>
                    </td>
                    <td><span class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('d M Y') }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('petugas.klaim.show', $item->id_klaim) }}" class="btn btn-sm btn-pnp-outline px-3 shadow-sm">Periksa</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted small">Belum ada antrian klaim yang masuk saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection