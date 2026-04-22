@extends('layouts.app')
@section('title', 'Daftar Dosen - Admin Panel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Dosen</h4>
        <p class="text-muted small mb-0">Kelola akun dosen, pantau profil akademik, dan status verifikasi NIP.</p>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Dosen</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">NIP</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">Akademik</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Kartu ID</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Verifikasi</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Status</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($dosens as $user)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->dosen->nama ?? $user->email) }}&background=f1f5f9&color=64748b" class="rounded-circle border me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $user->dosen->nama ?? 'Nama Belum Diisi' }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->dosen)
                                    <div class="text-muted fw-bold small">{{ $user->dosen->nip }}</div>
                                @else
                                    <span class="text-muted small italic">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark small" style="font-size: 0.75rem;">{{ $user->dosen->jurusan ?? '-' }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $user->dosen->prodi ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                @if($user->dosen && $user->dosen->foto_id)
                                    <img src="{{ asset('storage/' . $user->dosen->foto_id) }}" 
                                         class="rounded border shadow-sm" 
                                         width="50" height="35" 
                                         style="object-fit: cover; cursor: pointer;" 
                                         data-bs-toggle="modal" 
                                         data-bs-target="#idModal{{ $user->id_user }}"
                                         title="Klik untuk memperbesar">
                                @else
                                    <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->dosen && $user->dosen->nip_verified)
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">
                                        <i class="bi bi-patch-check-fill me-1"></i> TERVERIFIKASI
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">
                                        BELUM
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->status_akun == 'aktif')
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #0A4174;"></div>
                                        <span class="fw-bold text-dark small">AKTIF</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></div>
                                        <span class="fw-bold text-danger small">DIBLOKIR</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($user->dosen)
                                    <form action="{{ route('admin.users.verify_nim', $user->id_user) }}" method="POST" id="form-nip-{{ $user->id_user }}">
                                        @csrf
                                        <button type="button" class="btn btn-light btn-sm rounded-pill p-2" title="Verifikasi NIP"
                                            onclick="confirmAction('form-nip-{{ $user->id_user }}', 'Ubah Status Verifikasi?', 'NIP: {{ $user->dosen->nip }}')">
                                            <i class="bi bi-shield-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <button type="button" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id_user }}">
                                        Kelola
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal ID Enlarge --}}
                        @if($user->dosen && $user->dosen->foto_id)
                        <div class="modal fade" id="idModal{{ $user->id_user }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0" style="border-radius: 20px;">
                                    <div class="modal-header border-0 p-4">
                                        <h5 class="fw-bold mb-0"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Kartu Pegawai — {{ $user->dosen->nama ?? 'Dosen' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center p-4 pt-0">
                                        <img src="{{ asset('storage/' . $user->dosen->foto_id) }}" class="img-fluid rounded-4 shadow" style="max-height: 500px;" alt="ID Card">
                                        <div class="mt-3 text-muted small">
                                            <strong>NIP:</strong> {{ $user->dosen->nip }} |
                                            <strong>Status:</strong> {{ $user->dosen->nip_verified ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                        @if(!$user->dosen->nip_verified)
                                        <form action="{{ route('admin.users.verify_nim', $user->id_user) }}" method="POST" id="form-nip-modal-{{ $user->id_user }}">
                                            @csrf
                                            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm"
                                                onclick="confirmAction('form-nip-modal-{{ $user->id_user }}', 'Verifikasi NIP?', 'NIP {{ $user->dosen->nip }} akan diverifikasi berdasarkan foto kartu pegawai.')">
                                                <i class="bi bi-patch-check-fill me-1"></i> Verifikasi NIP
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Modal Kelola Akun -->
                        <div class="modal fade" id="editModal{{ $user->id_user }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0" style="border-radius: 20px;">
                                    <div class="modal-header p-4 border-0">
                                        <h5 class="fw-bold mb-0">Kelola Akun Dosen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST" id="form-edit-{{ $user->id_user }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 pt-0">
                                            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->dosen->nama ?? $user->email) }}&background=ffffff&color=000000" class="rounded-circle border" width="48" height="48">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $user->dosen->nama ?? 'Nama Belum Diisi' }}</div>
                                                    <div class="text-muted small">{{ $user->email }}</div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Poin Reputasi</label>
                                                <input type="number" class="form-control border-2" name="poin" value="{{ $user->poin }}" min="0" required style="border-radius: 10px; font-weight: 700;">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Status Operasional</label>
                                                <select id="status_akun_{{ $user->id_user }}" name="status_akun" class="form-select border-2" required style="border-radius: 10px; font-weight: 700;">
                                                    <option value="aktif" {{ $user->status_akun == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                                                    <option value="nonaktif" {{ $user->status_akun == 'nonaktif' ? 'selected' : '' }}>DIBLOKIR / NONAKTIF</option>
                                                </select>
                                                <div class="form-text mt-2 small text-danger"><i class="bi bi-info-circle me-1"></i> Memblokir akun akan menghentikan akses login dosen.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer p-4 pt-0 border-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="confirmAction('form-edit-{{ $user->id_user }}', 'Simpan Perubahan?', 'Data akun akan diperbarui.')">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $dosens->links('pagination::bootstrap-5') }}
</div>
@endsection
