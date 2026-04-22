@extends('layouts.app')
@section('title', 'Daftar Barang Temuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Barang Temuan</h4>
        <p class="text-muted small mb-0">Kelola inventaris barang yang ditemukan di lingkungan kampus PNP.</p>
    </div>
    <a href="{{ route('petugas.temuan.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Barang
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('petugas.temuan.index') }}" method="GET" class="row g-2">
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
                    <option value="menunggu_diserahkan" {{ request('status') == 'menunggu_diserahkan' ? 'selected' : '' }}>Belum Diserahkan</option>
                    <option value="sudah_diterima" {{ request('status') == 'sudah_diterima' ? 'selected' : '' }}>Diterima Petugas</option>
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
                        <th class="border-0 small fw-bold text-muted text-uppercase">Kategori</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Tgl Ditemukan</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Penyerahan</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($barangTemuan as $item)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                            <div class="text-muted small">{{ $item->lokasi_nama }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.7rem;">{{ $item->kategori->nama_kategori }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $item->tanggal_ditemukan->format('d M Y') }}</div>
                            <div class="text-muted small">ID: #{{ $item->id_barang }}</div>
                        </td>
                        <td>
                            @php
                                $s = match($item->status_penyerahan) {
                                    'sudah_diterima' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'DITERIMA PETUGAS'],
                                    'menunggu_diserahkan' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'BELUM DISERAHKAN'],
                                    default => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => strtoupper($item->status_penyerahan)],
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.7rem; letter-spacing: 0.5px;">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('petugas.temuan.show', $item->id_barang) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm border-0">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada barang temuan yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $barangTemuan->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection

