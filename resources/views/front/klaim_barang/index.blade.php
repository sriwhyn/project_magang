@extends('layouts.front')
@section('title', 'Klaim Barang Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate-fade">
    <div>
        <h4 class="fw-bold mb-1">Klaim Barang</h4>
        <p class="text-muted mb-0 small">Daftar klaim yang Anda ajukan</p>
    </div>
</div>

<div class="row g-3">
    @forelse($klaim as $item)
    <div class="col-md-6 col-lg-4 animate-fade">
        <div class="sc-card h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">{{ $item->barangTemuan->nama_barang ?? 'Barang' }}</h6>
                    @php
                        $statusColor = match($item->status_klaim) { 'menunggu' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger', default => 'secondary' };
                    @endphp
                    <span class="sc-badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">{{ ucfirst($item->status_klaim) }}</span>
                </div>
                <div class="small text-muted mb-2">
                    <i class="bi bi-tag me-1"></i>{{ $item->kategori->nama_kategori ?? '-' }}
                    <span class="mx-1">|</span>
                    <i class="bi bi-calendar me-1"></i>{{ $item->tanggal_pengajuan?->format('d M Y') ?? '-' }}
                </div>
                <a href="{{ route('mahasiswa.klaim.show', $item->id_klaim) }}" class="btn btn-sm btn-pnp-primary rounded-pill px-3">Detail</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="sc-card text-center py-5">
            <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Belum ada klaim yang diajukan. Untuk melakukan klaim, silakan pilih barang temuan yang dipublikasi pada halaman <strong>Beranda</strong>.</p>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $klaim->links() }}</div>
@endsection

