@extends('layouts.app')
@section('title', 'Tambah Kategori')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 class="fw-bold mb-1">Tambah Kategori Baru</h4>
        <p class="text-muted small mb-0">Kelola kategori untuk klasifikasi laporan kehilangan dan kerusakan.</p>
    </div>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Batal
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-6">
        <div class="form-card-simple shadow-sm">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 border-0 bg-danger bg-opacity-10 text-danger mb-4">
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('admin.kategori.store') }}" method="POST">
                @csrf
                <div class="form-section-label">Detail Kategori</div>
                <div class="form-group-custom">
                    <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-simple" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" required placeholder="Contoh: Elektronik, Toilet, Kendaraan">
                </div>
                <div class="form-group-custom">
                    <label for="tipe">Tipe Kategori <span class="text-danger">*</span></label>
                    <select class="form-select input-simple" id="tipe" name="tipe" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="kehilangan" {{ old('tipe') == 'kehilangan' ? 'selected' : '' }}>Kehilangan (Barang Pribadi)</option>
                        <option value="kerusakan" {{ old('tipe') == 'kerusakan' ? 'selected' : '' }}>Kerusakan (Fasilitas Kampus)</option>
                        <option value="semua" {{ old('tipe') == 'semua' ? 'selected' : '' }}>Semua (Berlaku untuk keduanya)</option>
                    </select>
                    <small class="text-muted mt-2 d-block px-1" style="font-size: 0.75rem;">Platform akan memfilter kategori berdasarkan tipe laporan.</small>
                </div>
                <div class="d-grid pt-3">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
