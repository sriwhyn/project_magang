@extends('layouts.app')
@section('title', 'Audit Akun Pengguna - Admin Panel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Audit Akun Pengguna</h4>
        <p class="text-muted small mb-0">Daftar pendaftar Mahasiswa & Dosen yang menunggu audit kelengkapan data.</p>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 24px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; border-bottom: 2px solid #f1f5f9;">
                    <tr>
                        <th class="ps-4 py-4 border-0 small fw-bold text-muted text-uppercase">Nama Pengguna</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Identitas</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Role</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Waktu Daftar</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Dokumen ID</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingUsers as $user)
                    @php
                        $profile = $user->role === 'mahasiswa' ? $user->mahasiswa : $user->dosen;
                        $idLabel = $user->role === 'mahasiswa' ? 'NIM' : 'NIP';
                        $idValue = $user->role === 'mahasiswa' ? ($profile->nim ?? '-') : ($profile->nip ?? '-');
                        $docPath = $user->role === 'mahasiswa' ? ($profile->foto_ktm ?? null) : ($profile->foto_id ?? null);
                        $roleBadge = $user->role === 'mahasiswa' ? 'bg-info' : 'bg-warning';
                    @endphp
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10" style="width: 44px; height: 44px; font-weight: 800;">
                                    {{ substr($profile->nama ?? 'U', 0, 1) }}
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $profile->nama ?? 'N/A' }}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">{{ $idLabel }}</div>
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;">{{ $idValue }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $roleBadge }} bg-opacity-10 text-{{ str_replace('bg-', '', $roleBadge) }} rounded-pill px-3 py-1 border border-{{ str_replace('bg-', '', $roleBadge) }} border-opacity-20 text-capitalize" style="font-size: 0.75rem;">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $user->created_at->diffForHumans() }}
                        </td>
                        <td class="text-center">
                            @if($docPath)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-20" style="font-size: 0.7rem; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $user->id_user }}">
                                    <i class="bi bi-file-earmark-image me-1"></i> Tersedia
                                </span>
                            @else
                                <span class="badge bg-light text-muted rounded-pill px-3 py-2 border" style="font-size: 0.7rem;">Kosong</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#detailModal{{ $user->id_user }}">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </button>
                                <form action="{{ route('admin.users.verify.activate', $user->id_user) }}" method="POST" id="form-ok-row-{{ $user->id_user }}">
                                    @csrf
                                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm"
                                        onclick="confirmAction('form-ok-row-{{ $user->id_user }}', 'Konfirmasi Data?', 'Verifikasi akun ini?')">
                                        <i class="bi bi-check-lg"></i> OK
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Detail Pengguna -->
                    <div class="modal fade" id="detailModal{{ $user->id_user }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
                                <div class="modal-body p-0">
                                    <div class="row g-0">
                                        <div class="col-lg-6 bg-light p-4 d-flex flex-column align-items-center justify-content-center" style="background: #f1f5f9 !important; border-right: 1px solid #e2e8f0;">
                                            <p class="small fw-bold text-muted text-uppercase mb-3">
                                                <i class="bi bi-card-image me-1"></i> 
                                                {{ $user->role === 'mahasiswa' ? 'Foto KTM Asli' : 'Foto Kartu Pegawai' }}
                                            </p>
                                            @if($docPath)
                                                <img src="{{ asset('storage/' . $docPath) }}" class="img-fluid rounded-4 shadow-sm border-white border-4" style="max-height: 400px; width: 100%; object-fit: contain;" alt="ID Card">
                                                <a href="{{ asset('storage/' . $docPath) }}" target="_blank" class="btn btn-sm btn-white bg-white rounded-pill px-3 shadow-sm border mt-3 small fw-bold">
                                                    <i class="bi bi-zoom-in me-1"></i> Perbesar Foto
                                                </a>
                                            @else
                                                <div class="text-center py-5 opacity-25">
                                                    <i class="bi bi-image-fill display-1"></i>
                                                    <p class="fw-bold">Tidak ada foto</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-6 p-5">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <h4 class="fw-bold mb-1">Informasi {{ ucfirst($user->role) }}</h4>
                                                    <p class="text-muted small mb-0">Pastikan {{ $idLabel }} sesuai dengan dokumen.</p>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="mb-5">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Nama Lengkap</label>
                                                        <div class="fw-bold text-dark mb-0">{{ $profile->nama }}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">{{ $idLabel }}</label>
                                                        <div class="fw-bold text-primary mb-0">{{ $idValue }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Jurusan</label>
                                                        <div class="text-dark fw-bold" style="font-size: 0.9rem;">{{ $profile->jurusan ?? '-' }}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Program Studi</label>
                                                        <div class="text-dark fw-bold" style="font-size: 0.9rem;">{{ $profile->prodi ?? '-' }}</div>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Email & WhatsApp</label>
                                                    <div class="text-dark fw-medium">{{ $user->email }}</div>
                                                    <div class="text-dark">{{ $user->no_hp ?? '-' }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.users.verify.reject', $user->id_user) }}" method="POST" class="flex-grow-1" id="form-ban-modal-{{ $user->id_user }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold"
                                                        onclick="confirmAction('form-ban-modal-{{ $user->id_user }}', 'Tolak & Ban?', 'Pengguna akan diblokir.', 'error')">
                                                        Ban Akun
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.users.verify.activate', $user->id_user) }}" method="POST" class="flex-grow-1" id="form-ok-modal-{{ $user->id_user }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow"
                                                        onclick="confirmAction('form-ok-modal-{{ $user->id_user }}', 'Konfirmasi Sesuai?', 'Verifikasi akun ini?')">
                                                        Verifikasi OK
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="opacity-25 pb-2">
                                <i class="bi bi-person-check fs-1"></i>
                            </div>
                            <h6 class="fw-bold text-muted">Semua Pengguna Telah Terverifikasi</h6>
                            <p class="text-muted small mb-0">Antrean audit saat ini kosong.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $pendingUsers->links('pagination::bootstrap-5') }}
</div>
@endsection
