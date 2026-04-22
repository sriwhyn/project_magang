@extends(\Auth::user()->role === 'admin' ? 'layouts.app' : 'layouts.front')
@section('title', 'Detail Laporan - ' . $laporanKerusakan->judul_laporan)

@push('styles')
<style>
    .page-header-simple {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-status {
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .card-simple {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        overflow: hidden;
    }

    .label-section {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        display: block;
    }

    .info-list { width: 100%; }
    .info-list tr { border-bottom: 1px solid #f1f5f9; }
    .info-list tr:last-child { border-bottom: none; }
    .info-list th { padding: 14px 0; font-weight: 600; color: #64748b; font-size: 0.9rem; width: 40%; }
    .info-list td { padding: 14px 0; font-weight: 700; color: #1e293b; font-size: 0.9rem; text-align: right; }

    .photo-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
        margin-top: 1rem;
    }

    .photo-thumb {
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .photo-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .sticky-panel {
        position: sticky;
        top: 6.5rem;
    }

</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('petugas.kerusakan.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="page-header-simple shadow-sm">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h4 class="fw-bold mb-0 text-dark">{{ $laporanKerusakan->judul_laporan }}</h4>
            <span class="text-muted small fw-bold">ID: #{{ $laporanKerusakan->id_laporan }}</span>
        </div>
        <div class="text-muted small">Dilaporkan pada {{ $laporanKerusakan->tanggal_lapor->format('d M Y') }} jam {{ $laporanKerusakan->created_at->format('H:i') }}</div>
    </div>
    @php 
        $s = match($laporanKerusakan->status_perbaikan) { 
            'dilaporkan' => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => 'Dilaporkan'], 
            'menunggu_petugas' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'Menunggu Petugas'], 
            'dikerjakan' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'label' => 'Sedang Dikerjakan'], 
            'menunggu_validasi_admin' => ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'label' => 'Menunggu Validasi'], 
            'selesai' => ['bg' => '#BDD8E9', 'color' => '#001D39', 'label' => 'Selesai'], 
            default => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Ditolak'] 
        }; 
    @endphp
    <div class="badge-status shadow-sm" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }};">
        {{ $s['label'] }}
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-simple p-4 p-md-5 shadow-sm">
            <div class="mb-5">
                <span class="label-section">Informasi Laporan</span>
                <table class="info-list">
                    <tr><th>Kategori</th><td><span class="badge bg-light text-dark border px-3 rounded-pill">{{ $laporanKerusakan->kategori->nama_kategori }}</span></td></tr>
                    <tr><th>Email Pelapor</th><td class="text-primary">{{ $laporanKerusakan->pelapor->email }}</td></tr>
                    <tr><th>Lokasi Kerusakan</th><td>{{ $laporanKerusakan->lokasi_kerusakan }}</td></tr>
                    <tr><th>Petugas Perbaikan</th>
                        <td>
                            @if($laporanKerusakan->petugas)
                                <div>{{ $laporanKerusakan->petugas->nama }}</div>
                                <div class="small text-muted fw-normal">{{ $laporanKerusakan->petugas->jabatan }}</div>
                            @else
                                <span class="text-muted fw-normal italic">Belum ditentukan</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="mb-5">
                <span class="label-section">Deskripsi Kerusakan</span>
                <div class="p-4 bg-light rounded-4 border-0 text-dark" style="line-height: 1.6;">
                    {{ $laporanKerusakan->deskripsi }}
                </div>
            </div>

            @if($laporanKerusakan->gambar && is_array($laporanKerusakan->gambar) && count($laporanKerusakan->gambar) > 0)
            <div class="mb-5">
                <span class="label-section">Foto Bukti Kerusakan</span>
                <div class="photo-preview">
                    @foreach($laporanKerusakan->gambar as $path)
                    <div class="photo-thumb shadow-sm">
                        <img src="{{ asset('storage/' . $path) }}" alt="Foto">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($laporanKerusakan->foto_bukti_pengerjaan)
            @php $fotoBukti = is_array($laporanKerusakan->foto_bukti_pengerjaan) ? $laporanKerusakan->foto_bukti_pengerjaan : json_decode($laporanKerusakan->foto_bukti_pengerjaan, true); @endphp
            @if($fotoBukti && count($fotoBukti) > 0)
            <div class="mb-5">
                <span class="label-section"><i class="bi bi-camera-fill me-1 text-success"></i> Foto Bukti Pengerjaan</span>
                <div class="photo-preview">
                    @foreach($fotoBukti as $path)
                    <div class="photo-thumb shadow-sm" style="border-color: #10b981;">
                        <img src="{{ asset('storage/' . $path) }}" alt="Bukti Pengerjaan">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            @if($laporanKerusakan->catatan_petugas)
            <div>
                <span class="label-section">Catatan Petugas</span>
                <div class="p-3 rounded-4 bg-light border-start border-4 border-primary">
                    {{ $laporanKerusakan->catatan_petugas }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-simple p-4 shadow-sm sticky-panel">
            <span class="label-section">Update Status</span>
            
            @if($laporanKerusakan->status_perbaikan == 'selesai')
                <div class="text-center py-4">
                    <i class="bi bi-check-circle-fill text-success display-4 d-block mb-3"></i>
                    <h5 class="fw-bold">Laporan Selesai</h5>
                    <p class="text-muted small">Laporan ini sudah diperbaiki dan divalidasi oleh Admin.</p>
                </div>
            @elseif(\Auth::user()->role === 'petugas' && $laporanKerusakan->status_perbaikan == 'menunggu_validasi_admin')
                <div class="text-center py-4">
                    <i class="bi bi-hourglass-split text-warning display-4 d-block mb-3"></i>
                    <h5 class="fw-bold">Menunggu Validasi</h5>
                    <p class="text-muted small">Laporan ini sudah Anda selesaikan dan sedang menunggu verifikasi dari Admin.</p>
                </div>
            @else
                <form action="{{ route('petugas.kerusakan.update', $laporanKerusakan->id_laporan) }}" method="POST" id="form-update" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if(\Auth::user()->role === 'admin')
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Tugaskan Petugas</label>
                            <select class="form-select form-select-sm border-2" name="id_petugas" style="border-radius: 8px;">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($semuaPetugas as $pt)
                                    <option value="{{ $pt->id_petugas }}" {{ $laporanKerusakan->id_petugas == $pt->id_petugas ? 'selected' : '' }}>
                                        {{ $pt->nama }} ({{ $pt->jabatan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Status / Validasi</label>
                            <select class="form-select form-select-sm border-2" name="status_perbaikan" required style="border-radius: 8px;">
                                <option value="menunggu_petugas" {{ $laporanKerusakan->status_perbaikan == 'menunggu_petugas' ? 'selected' : '' }}>MENUNGGU</option>
                                <option value="dikerjakan" {{ $laporanKerusakan->status_perbaikan == 'dikerjakan' ? 'selected' : '' }}>SEDANG DIKERJAKAN</option>
                                <option value="menunggu_validasi_admin" {{ $laporanKerusakan->status_perbaikan == 'menunggu_validasi_admin' ? 'selected' : '' }}>MENUNGGU VALIDASI</option>
                                <option value="selesai" {{ $laporanKerusakan->status_perbaikan == 'selesai' ? 'selected' : '' }} class="text-success fw-bold">SETUJUI & SELESAI</option>
                            </select>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Status Pengerjaan</label>
                            <select class="form-select form-select-sm border-2" name="status_perbaikan" required style="border-radius: 8px;" id="selectStatus">
                                <option value="menunggu_petugas" {{ $laporanKerusakan->status_perbaikan == 'menunggu_petugas' ? 'selected' : '' }}>MENUNGGU</option>
                                <option value="dikerjakan" {{ $laporanKerusakan->status_perbaikan == 'dikerjakan' ? 'selected' : '' }}>SEDANG DIKERJAKAN</option>
                                <option value="menunggu_validasi_admin" {{ $laporanKerusakan->status_perbaikan == 'menunggu_validasi_admin' ? 'selected' : '' }}>SELESAI (KIRIM KE ADMIN)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Catatan Perbaikan</label>
                            <textarea class="form-control form-control-sm border-2" name="catatan_petugas" rows="2" placeholder="Detail perbaikan..." style="border-radius: 8px;">{{ $laporanKerusakan->catatan_petugas }}</textarea>
                        </div>

                        <div class="mb-4" id="fotoBuktiSection">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                <i class="bi bi-camera me-1"></i> Foto Bukti
                            </label>
                            <input type="file" class="form-control form-control-sm border-2" name="foto_bukti_pengerjaan[]" multiple accept="image/*" style="border-radius: 8px;">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold py-2 shadow-sm">
                        Simpan Perubahan
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
