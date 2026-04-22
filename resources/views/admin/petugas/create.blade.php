@extends('layouts.app')
@section('title', 'Tambah Petugas')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 class="fw-bold mb-1">Tambah Petugas Baru</h4>
        <p class="text-muted small mb-0">Daftarkan akun staf baru untuk mengelola operasional kampus.</p>
    </div>
    <a href="{{ route('admin.akun_petugas.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Batal
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="form-card-simple shadow-sm">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 border-0 bg-danger bg-opacity-10 text-danger mb-4">
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('admin.akun_petugas.store') }}" method="POST">
                @csrf

                <div class="form-section-label">Informasi Personal</div>
                <div class="row">
                    <div class="col-md-12 form-group-custom">
                        <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-simple" id="nama" name="nama" value="{{ old('nama') }}" required placeholder="Nama lengkap">
                    </div>
                </div>

                <div class="form-group-custom">
                    <label for="jabatan">Jabatan / Kompetensi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-simple" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Teknisi Listrik, Staff Keamanan">
                </div>

                <div class="form-section-label mt-4">Kredensial Login</div>
                <div class="row">
                    <div class="col-md-6 form-group-custom">
                        <label for="emailPreview">Email Login (Otomatis)</label>
                        <input type="text" class="form-control input-simple bg-light" id="emailPreview" readonly placeholder="Otomatis dari nama petugas" style="cursor: not-allowed; color: #475569;">
                        <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;"><i class="bi bi-lock-fill me-1 text-success"></i>Email dibuat otomatis dari nama.</small>
                    </div>
                    <div class="col-md-6 form-group-custom">
                        <label for="passwordField">Password <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-simple" name="password" id="passwordField" required placeholder="Ketik password manual">
                    </div>
                </div>

                <div class="d-grid pt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm">
                        <i class="bi bi-person-plus-fill me-2"></i>Simpan Akun Petugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generatePassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('passwordField').value = password;
}

document.addEventListener('DOMContentLoaded', function() {
    const namaField = document.getElementById('nama');
    const emailPreview = document.getElementById('emailPreview');

    function updateEmailPreview() {
        const nama = namaField.value.trim();
        if (nama) {
            let cleanName = nama.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '.');
            emailPreview.value = cleanName + '@petugas.pnp.ac.id';
        } else {
            emailPreview.value = '';
        }
    }

    namaField.addEventListener('input', updateEmailPreview);

    // Auto-generate password saat halaman load
    if (!document.getElementById('passwordField').value) {
        generatePassword();
    }

    // Jika ada old nama, update preview
    if (namaField.value) {
        updateEmailPreview();
    }
});
</script>
@endpush
