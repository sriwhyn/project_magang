@extends('layouts.app')
@section('title', 'Daftar Laporan Kerusakan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Laporan Kerusakan</h4>
        <p class="text-muted small mb-0">Manajement perbaikan fasilitas kampus berdasarkan laporan mahasiswa.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('petugas.kerusakan.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari Judul Laporan atau Lokasi..." value="{{ request('q') }}">
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
                    <option value="dilaporkan" {{ request('status') == 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
                    <option value="menunggu_petugas" {{ request('status') == 'menunggu_petugas' ? 'selected' : '' }}>Menunggu Petugas</option>
                    <option value="dikerjakan" {{ request('status') == 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                    <option value="menunggu_validasi_admin" {{ request('status') == 'menunggu_validasi_admin' ? 'selected' : '' }}>Validasi</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Laporan</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Fasilitas & Lokasi</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Tgl Penugasan</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Status Perbaikan</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($laporan as $item)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $item->judul_laporan }}</div>
                            <div class="text-muted small">Dilaporkan {{ $item->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $item->fasilitas->nama_fasilitas ?? 'Umum' }}</div>
                            <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i> {{ $item->lokasi_kerusakan }}</div>
                        </td>
                        <td>
                            @if($item->tanggal_penugasan)
                                <div class="small fw-semibold text-dark">{{ $item->tanggal_penugasan->format('d M Y') }}</div>
                                <div class="text-muted small">Oleh Admin</div>
                            @else
                                <span class="text-muted small italic">Belum Ditugaskan</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $s = match($item->status_perbaikan) {
                                    'menunggu_petugas' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'MENUNGGU'],
                                    'dikerjakan' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'label' => 'DIPROSES'],
                                    'menunggu_validasi_admin' => ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'label' => 'VALIDASI'],
                                    'selesai' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'SELESAI'],
                                    default => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => 'DILAPORKAN'],
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.7rem; letter-spacing: 0.5px;">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('petugas.kerusakan.show', $item->id_laporan) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm border-0">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada laporan kerusakan yang tercatat.</td>
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

