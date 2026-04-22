@extends('layouts.app')
@section('title', 'Daftar Klaim Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Klaim Barang</h4>
        <p class="text-muted small mb-0">Verifikasi pengajuan klaim barang temuan yang diajukan oleh mahasiswa.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('petugas.klaim.index') }}" method="GET" class="row g-2">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari Nama Pengaju atau Nama Barang..." value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
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
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Pengaju</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Barang Temuan</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Tgl Pengajuan</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Status</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($klaim as $item)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $item->nama_pemilik }}</div>
                            <div class="text-muted small">{{ $item->pengaju->email }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $item->barangTemuan->nama_barang ?? 'Barang Terhapus' }}</div>
                            @if($item->kategori)
                                <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.7rem;">{{ $item->kategori->nama_kategori }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $item->tanggal_pengajuan->format('d M Y') }}</div>
                            <div class="text-muted small opacity-75">Diajukan</div>
                        </td>
                        <td>
                            @php
                                $s = match($item->status_klaim) {
                                    'disetujui' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'DISETUJUI'],
                                    'ditolak' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'DITOLAK'],
                                    default => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'MENUNGGU'],
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.7rem; letter-spacing: 0.5px;">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('petugas.klaim.show', $item->id_klaim) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm border-0">
                                Verifikasi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pengajuan klaim barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $klaim->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection

