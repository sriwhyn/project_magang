@extends('layouts.app')
@section('title', 'Edit Petugas')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 class="fw-bold mb-1">Edit Akun Petugas</h4>
        <p class="text-muted small mb-0">Ubah informasi personal atau reset password staf.</p>
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

            <form action="{{ route('admin.akun_petugas.update', $petugas->id_petugas) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-section-label">Informasi Personal</div>
                <div class="row">
                    <div class="col-md-12 form-group-custom">
                        <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-simple" id="nama" name="nama" value="{{ old('nama', $petugas->nama) }}" required placeholder="Nama lengkap">
                    </div>
                </div>
                <small class="text-muted mt-1 d-block mb-3" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Jika nama diubah, email login juga otomatis berubah.</small>

                <div class="form-group-custom">
                    <label for="jabatan">Jabatan / Kompetensi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-simple" id="jabatan" name="jabatan" value="{{ old('jabatan', $petugas->jabatan) }}" required placeholder="Contoh: Teknisi Listrik, Staff Keamanan">
                </div>

                <div class="form-section-label mt-4">Kredensial Login</div>
                <div class="row">
                    <div class="col-md-7 form-group-custom">
                        <label>Email Login Saat Ini</label>
                        <input type="text" class="form-control input-simple bg-light" id="emailPreview" readonly value="{{ $petugas->user->email ?? '' }}" style="cursor: not-allowed; color: #475569;">
                        <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;"><i class="bi bi-lock-fill me-1 text-success"></i>Email otomatis sesuai nama.</small>
                    </div>
                    <div class="col-md-5 form-group-custom">
                        <label for="passwordField">Password Baru</label>
                        <input type="text" class="form-control input-simple" name="password" id="passwordField" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>

                <div class="d-grid pt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm">
                        <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
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
});
</script>
@endpush
