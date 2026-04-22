@extends('layouts.app')
@section('title', 'Daftar Mahasiswa - Admin Panel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Mahasiswa</h4>
        <p class="text-muted small mb-0">Kelola akun mahasiswa, verifikasi NIM, dan pantau poin reputasi pengguna.</p>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Mahasiswa</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase">NIM</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">KTM</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Verifikasi</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Poin</th>
                        <th class="border-0 text-center small fw-bold text-muted text-uppercase">Status</th>
                        <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($mahasiswas as $user)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->mahasiswa->nama ?? $user->email) }}&background=f1f5f9&color=64748b" class="rounded-circle border me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $user->mahasiswa->nama ?? 'Nama Belum Diisi' }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->mahasiswa)
                                    <div class="text-muted fw-bold small">{{ $user->mahasiswa->nim }}</div>
                                @else
                                    <span class="text-muted small italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->mahasiswa && $user->mahasiswa->foto_ktm)
                                    <img src="{{ asset('storage/' . $user->mahasiswa->foto_ktm) }}" 
                                         class="rounded border shadow-sm" 
                                         width="50" height="35" 
                                         style="object-fit: cover; cursor: pointer;" 
                                         data-bs-toggle="modal" 
                                         data-bs-target="#ktmModal{{ $user->id_user }}"
                                         title="Klik untuk memperbesar">
                                @else
                                    <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->mahasiswa && $user->mahasiswa->nim_verified)
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
                                <div class="fw-bold text-dark fs-5">{{ $user->poin }}</div>
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
                                    @if($user->mahasiswa)
                                    <form action="{{ route('admin.users.verify_nim', $user->id_user) }}" method="POST" id="form-nim-{{ $user->id_user }}">
                                        @csrf
                                        <button type="button" class="btn btn-light btn-sm rounded-pill p-2" title="Verifikasi NIM"
                                            onclick="confirmAction('form-nim-{{ $user->id_user }}', 'Ubah Status Verifikasi?', 'NIM: {{ $user->mahasiswa->nim }}')">
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

                        {{-- Modal KTM Enlarge --}}
                        @if($user->mahasiswa && $user->mahasiswa->foto_ktm)
                        <div class="modal fade" id="ktmModal{{ $user->id_user }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0" style="border-radius: 20px;">
                                    <div class="modal-header border-0 p-4">
                                        <h5 class="fw-bold mb-0"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Foto KTM — {{ $user->mahasiswa->nama ?? 'Mahasiswa' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center p-4 pt-0">
                                        <img src="{{ asset('storage/' . $user->mahasiswa->foto_ktm) }}" class="img-fluid rounded-4 shadow" style="max-height: 500px;" alt="KTM">
                                        <div class="mt-3 text-muted small">
                                            <strong>NIM:</strong> {{ $user->mahasiswa->nim }} |
                                            <strong>Status:</strong> {{ $user->mahasiswa->nim_verified ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                        @if(!$user->mahasiswa->nim_verified)
                                        <form action="{{ route('admin.users.verify_nim', $user->id_user) }}" method="POST" id="form-nim-modal-{{ $user->id_user }}">
                                            @csrf
                                            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm"
                                                onclick="confirmAction('form-nim-modal-{{ $user->id_user }}', 'Verifikasi NIM?', 'NIM {{ $user->mahasiswa->nim }} akan diverifikasi berdasarkan foto KTM.')">
                                                <i class="bi bi-patch-check-fill me-1"></i> Verifikasi NIM
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
                                        <h5 class="fw-bold mb-0">Kelola Akun Mahasiswa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST" id="form-edit-{{ $user->id_user }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 pt-0">
                                            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->mahasiswa->nama ?? $user->email) }}&background=ffffff&color=000000" class="rounded-circle border" width="48" height="48">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $user->mahasiswa->nama ?? 'Nama Belum Diisi' }}</div>
                                                    <div class="text-muted small">{{ $user->email }}</div>
                                                </div>
                                            </div>

                                            @php $banding = $user->pengajuanBanding->first(); @endphp
                                            @if($user->status_akun == 'nonaktif' && $banding && $banding->status == 'menunggu')
                                            <div class="alert alert-warning border-0 rounded-4 p-3 mb-4">
                                                <div class="fw-bold small text-uppercase mb-2">Pengajuan Pemulihan</div>
                                                <div class="p-3 bg-white bg-opacity-50 rounded-3 mb-3 small italic">"{{ $banding->alasan }}"</div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 w-100" onclick="document.getElementById('status_akun_{{ $user->id_user }}').value='aktif';">Aktifkan Akun</button>
                                                    <button type="button" onclick="confirmAction('formTolakBanding{{ $user->id_user }}', 'Tolak Pengajuan?', 'Tindakan ini permanen.', 'error')" class="btn btn-sm btn-outline-danger px-3 border-0 rounded-pill">Tolak</button>
                                                </div>
                                            </div>
                                            @endif

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
                                                <div class="form-text mt-2 small text-danger"><i class="bi bi-info-circle me-1"></i> Memblokir akun akan menghentikan akses login mahasiswa.</div>
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
                        <form id="formTolakBanding{{ $user->id_user }}" action="{{ route('admin.users.tolak_banding', $user->id_user) }}" method="POST" class="d-none">@csrf</form>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $mahasiswas->links('pagination::bootstrap-5') }}
</div>
@endsection
