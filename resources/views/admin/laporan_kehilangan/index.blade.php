@extends('layouts.app')
@section('title', 'Daftar Laporan Kehilangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Laporan Kehilangan</h4>
        <p class="text-muted small mb-0">Manajemen laporan kehilangan barang dari mahasiswa di lingkungan kampus.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('petugas.kehilangan.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari Nama Barang atau ID..." value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select bg-light border-0">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $cat)
                        <option value="{{ $cat->id_kategori }}" {{ request('category') == $cat->id_kategori ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="dicocokkan" {{ request('status') == 'dicocokkan' ? 'selected' : '' }}>Dicocokkan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Ditemukan</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100 rounded-3"><i class="bi bi-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Nama Barang</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Pelapor</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Tgl Kejadian</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Status</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($laporan as $item)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                            <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.7rem;">{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $item->user->email ?? 'User Terhapus' }}</div>
                            <div class="text-muted small">Mahasiswa</div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $item->tanggal_hilang?->format('d M Y') ?? 'N/A' }}</div>
                            <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i> {{ $item->lokasi_hilang }}</div>
                        </td>
                        <td>
                            @php
                                $s = match($item->status) {
                                    'menunggu' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'MENUNGGU'],
                                    'diproses' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'label' => 'DIPROSES'],
                                    'selesai' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'DITEMUKAN'],
                                    'ditolak' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'DITOLAK'],
                                    'dicocokkan' => ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'label' => 'DICOCOKKAN'],
                                    default => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => strtoupper($item->status)],
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.7rem; letter-spacing: 0.5px;">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('petugas.kehilangan.show', $item->id_laporan) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm border-0">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada laporan kehilangan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $laporan->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection

