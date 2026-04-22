@extends('layouts.app')
@section('title', 'Daftar Petugas & Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Petugas & Staf</h4>
        <p class="text-muted small mb-0">Kelola akun staf operasional dan teknisi yang bertugas menangani laporan kerusakan.</p>
    </div>
    <a href="{{ route('admin.akun_petugas.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Petugas
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Nama Petugas</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Email</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Jabatan / Unit</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($petugasList as $pt)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $pt->nama }}</div>
                                    <div class="text-muted small">NIK: {{ $pt->nik ?? 'Belum Diatur' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $pt->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.75rem;">{{ $pt->jabatan }}</span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.akun_petugas.edit', $pt->id_petugas) }}" class="btn btn-light btn-sm rounded-pill p-2 px-3 border" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.akun_petugas.destroy', $pt->id_petugas) }}" method="POST" id="form-hapus-{{ $pt->id_petugas }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-light btn-sm rounded-pill p-2 px-3 border text-danger" 
                                        onclick="confirmAction('form-hapus-{{ $pt->id_petugas }}', 'Hapus Akun Petugas?', 'Semua tugas aktif petugas ini akan menjadi tanpa petugas.', 'warning')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada data petugas terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('credential_email'))
        Swal.fire({
            icon: 'success',
            title: 'Akun Petugas Berhasil Dibuat!',
            html: `
                <div style="text-align:left; background:#f8fafc; border-radius:12px; padding:20px; margin-top:12px; border: 1px solid #e2e8f0;">
                    <table style="width:100%; font-size:0.9rem;">
                        <tr>
                            <td style="padding:8px 0; color:#64748b; font-weight:500;">Nama</td>
                            <td style="padding:8px 0; font-weight:700; text-align:right;">{{ session('credential_nama') }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #e2e8f0;">
                            <td style="padding:8px 0; color:#64748b; font-weight:500;">Email</td>
                            <td style="padding:8px 0; font-weight:700; text-align:right; color:#2563eb;">{{ session('credential_email') }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #e2e8f0;">
                            <td style="padding:8px 0; color:#64748b; font-weight:500;">Password</td>
                            <td style="padding:8px 0; font-weight:700; text-align:right; font-family:monospace; letter-spacing:1px; color:#dc2626;">{{ session('credential_password') }}</td>
                        </tr>
                    </table>
                </div>
                <p style="margin-top:16px; font-size:0.8rem; color:#ef4444; font-weight:600;">
                    <i class="bi bi-exclamation-triangle-fill"></i> Catat kredensial ini sekarang! Password tidak bisa dilihat lagi setelah menutup popup ini.
                </p>
            `,
            confirmButtonText: 'Sudah Dicatat',
            confirmButtonColor: '#3b82f6',
            allowOutsideClick: false,
            width: 480,
        });
    @endif
});
</script>
@endpush
