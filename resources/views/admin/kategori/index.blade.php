@extends('layouts.app')
@section('title', 'Manajemen Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Kategori</h4>
        <p class="text-muted small mb-0">Kelola daftar kategori untuk klasifikasi laporan kehilangan dan kerusakan.</p>
    </div>
    <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase" width="15%">ID</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase" width="45%">Nama Kategori</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase" width="20%">Tipe</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($kategori as $item)
                    <tr>
                        <td class="ps-4 py-3">
                            <span class="text-muted fw-bold small">#{{ str_pad($item->id_kategori, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->nama_kategori }}</div>
                        </td>
                        <td>
                            @php
                                $t = match($item->tipe) {
                                    'kehilangan' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'label' => 'KEHILANGAN'],
                                    'kerusakan' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'KERUSAKAN'],
                                    default => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => 'UMUM'],
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $t['bg'] }}; color: {{ $t['color'] }}; font-size: 0.7rem; letter-spacing: 0.5px;">
                                {{ $t['label'] }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.kategori.edit', $item->id_kategori) }}" class="btn btn-light btn-sm rounded-pill p-2 px-3 border me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.kategori.destroy', $item->id_kategori) }}" method="POST" class="d-inline" id="form-hapus-kategori-{{ $item->id_kategori }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-light btn-sm rounded-pill p-2 px-3 border text-danger" onclick="confirmAction('form-hapus-kategori-{{ $item->id_kategori }}', 'Hapus Kategori?', 'Tindakan ini permanen.', 'warning')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada kategori yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
