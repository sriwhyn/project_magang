@extends(\Auth::user()->role === 'admin' ? 'layouts.app' : 'layouts.front')
@section('title', 'Detail Barang - ' . $barangTemuan->nama_barang)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    /* Plain Admin Styling (Simple Bootstrap) */
    .page-header-simple {
        background: white; border-radius: 4px; padding: 15px; border: 1px solid #dee2e6;
        margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;
    }
    .info-list { width: 100%; border-collapse: collapse; }
    .info-list tr { border-bottom: 1px solid #f8fafc; }
    .info-list tr:last-child { border-bottom: none; }
    .info-list th { padding: 10px 0; font-weight: 500; color: #6c757d; font-size: 0.8rem; width: 40%; }
    .info-list td { padding: 10px 0; font-weight: 700; color: #212529; font-size: 0.8rem; text-align: right; }
    
    #map-admin { height: 250px; width: 100%; border-radius: 4px; border: 1px solid #dee2e6; }
    
    /* Plain Text Stepper (No Circles) */
    .text-step-done { color: #198754; font-weight: 700; }
    .text-step-active { color: #0d6efd; font-weight: 800; text-decoration: underline; }
    .text-step-upcoming { color: #adb5bd; }
    .step-separator { color: #dee2e6; margin: 0 4px; }
    
    .photo-thumb { cursor: zoom-in; transition: transform 0.2s; }
    .photo-thumb:hover { transform: scale(1.02); }
    .stepper-step { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; flex: 1; text-align: center; }
    .step-icon { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: white; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 0.5rem; transition: all 0.3s ease; font-size: 1.1rem; }
    .step-label { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.025em; }
    
    .stepper-step.completed .step-icon { background: #059669; border-color: #059669; color: white; }
    .stepper-step.completed .step-label { color: #059669; }
    .stepper-step.active .step-icon { border-color: #0d6efd; color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
    .stepper-step.active .step-label { color: #0d6efd; }
    
    @media (max-width: 768px) {
        .stepper-horizontal { flex-direction: column; gap: 1.5rem; }
        .stepper-horizontal::before { display: none; }
        .stepper-step { flex-direction: row; text-align: left; gap: 1rem; }
        .step-icon { margin-bottom: 0; }
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('petugas.temuan.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="page-header-simple border">
    <div>
        <h6 class="fw-bold mb-1">{{ $barangTemuan->nama_barang }}</h6>
        <div class="text-muted extra-small">ID Laporan: T{{ $barangTemuan->id_barang }} | Pelapor: {{ $barangTemuan->pelapor->email }}</div>
    </div>
    @php 
        $statusStyle = match($barangTemuan->status_verifikasi) { 
            'terverifikasi' => 'bg-success', 
            'ditolak' => 'bg-danger', 
            default => 'bg-warning' 
        }; 
    @endphp
    <span class="badge {{ $statusStyle }} px-3 py-1 rounded-0 small">{{ strtoupper($barangTemuan->status_verifikasi) }}</span>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border mb-4">
            <div class="card-body p-3">
                {{-- Text-Only Horizontal Stepper Admin (No circles) --}}
                @php
                    $isPending = ($barangTemuan->status_verifikasi == 'belum');
                    $isVerified = ($barangTemuan->status_verifikasi == 'terverifikasi');
                    $isAtAdmin = ($barangTemuan->status_penyerahan == 'sudah_diterima');
                    $isSelesai = $barangTemuan->status_klaim == 'selesai';
                @endphp
                <div class="mb-4 mt-2">
                    <h6 class="fw-bold small border-bottom pb-1 mb-2 text-uppercase">Spesifikasi Barang</h6>
                    <table class="info-list">
                        <tr><th>Kategori</th><td><span class="badge bg-primary rounded-0 px-2">{{ $barangTemuan->kategori->nama_kategori }}</span></td></tr>
                        <tr><th>Lokasi Ditemukan</th><td>{{ $barangTemuan->lokasi_nama }}</td></tr>
                        <tr><th>Waktu Ditemukan</th><td>{{ $barangTemuan->tanggal_ditemukan->format('d/m/Y') }}</td></tr>
                        @if($barangTemuan->merk)<tr><th>Merk / Seri</th><td>{{ $barangTemuan->merk }}</td></tr>@endif
                        @if($barangTemuan->tipe_model)<tr><th>Model</th><td>{{ $barangTemuan->tipe_model }}</td></tr>@endif
                        @if($barangTemuan->ukuran)<tr><th>Ukuran</th><td>{{ $barangTemuan->ukuran }}</td></tr>@endif
                        <tr>
                            <th>Status Fisik</th>
                            <td>
                                @if($barangTemuan->status_klaim == 'belum')
                                    <span class="text-success fw-bold">TERSEDIA</span>
                                @else
                                    <span class="text-muted fw-bold">DIAMBIL</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold small border-bottom pb-1 mb-2 text-uppercase">Keterangan</h6>
                    <div class="p-2 bg-light border text-muted small">
                        {{ $barangTemuan->deskripsi_singkat }}
                    </div>
                </div>

                @if($barangTemuan->latitude && $barangTemuan->longitude)
                <div class="mb-4">
                    <h6 class="fw-bold small border-bottom pb-1 mb-2 text-uppercase">Titik Koordinat</h6>
                    <div id="map-admin"></div>
                </div>
                @endif

                @if($barangTemuan->foto)
                <div>
                    <h6 class="fw-bold small border-bottom pb-1 mb-2 text-uppercase">Dokumentasi Foto</h6>
                    @php $fotos = is_array($barangTemuan->foto) ? $barangTemuan->foto : json_decode($barangTemuan->foto, true); @endphp
                    @if($fotos && count($fotos) > 0)
                        <div class="row g-2">
                            @foreach($fotos as $path)
                                <div class="col-4">
                                    <img src="{{ asset('storage/' . $path) }}" class="w-100 rounded-0 border photo-thumb admin-lightbox" style="aspect-ratio: 1/1; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @php
            $isPenipuan = \App\Models\RiwayatPelanggaran::where('id_user', $barangTemuan->id_user_pelapor)
                ->where('jenis_pelanggaran', 'penipuan_temuan')
                ->where('deskripsi', 'like', '%'.$barangTemuan->nama_barang.'%')
                ->exists();
            $isSelesai = $barangTemuan->status_klaim == 'selesai';
            $isVerified = ($barangTemuan->status_verifikasi == 'terverifikasi');
            $isReceived = ($barangTemuan->status_penyerahan == 'sudah_diterima');
        @endphp

        <div class="card-simple shadow-sm">
            <div class="p-3 border-bottom bg-light">
                <span class="label-section mb-0"><i class="bi bi-gear-fill me-1"></i> Panel Kontrol Admin</span>
            </div>

            <div class="p-4">
                @if($isSelesai)
                    {{-- TAHAP AKHIR: SUDAH DIAMBIL PEMILIK --}}
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                        <h6 class="fw-bold mb-1">Pengambilan Selesai</h6>
                        <p class="text-muted small mb-0">Barang sudah di tangan pemilik.</p>
                    </div>

                @elseif($isReceived)
                    {{-- TAHAP 3: BARANG SUDAH DI TANGAN ADMIN --}}
                    <div class="text-center py-3">
                        <i class="bi bi-patch-check-fill text-primary fs-1 d-block mb-2"></i>
                        <h6 class="fw-bold mb-1 text-primary">Barang Sudah Diamankan</h6>
                        <div class="p-2 bg-light rounded-3 small text-muted border mb-3">
                            <div>Status: <strong>Terverifikasi</strong></div>
                            <div>Fisik: <strong>Sudah di Admin</strong></div>
                        </div>
                        <hr>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Menunggu pemilik mengajukan Klaim Barang.</p>
                    </div>

                @elseif($isPenipuan)
                    <div class="alert alert-danger border-0 p-3 text-center mb-0" style="border-radius: 12px;">
                        <i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-1"></i>
                        <strong class="small d-block">Laporan Palsu/Penipuan</strong>
                    </div>

                @else
                    {{-- TAHAP 1 & 2: PROSES ADMIN --}}
                    @if(\Auth::user()->role === 'admin')
                        
                        {{-- 1. RESPON BANDING / APPEAL (SISTEM BARU) --}}
                        @php
                            $activeBanding = $barangTemuan->pengajuanBanding()->where('status', 'menunggu')->latest()->first();
                        @endphp
                        @if($activeBanding)
                            <div class="card border-danger mb-4 shadow-sm" style="border-radius: 12px; border-style: dashed;">
                                <div class="card-body p-3 bg-danger bg-opacity-10">
                                    <h6 class="fw-bold text-danger small mb-2"><i class="bi bi-megaphone-fill me-1"></i> Pengajuan Banding Baru!</h6>
                                    <div class="p-2 bg-white rounded border mb-2 small italic text-muted">"{{ $activeBanding->alasan }}"</div>
                                    
                                    @if($activeBanding->foto)
                                        <div class="mb-3">
                                            <p class="extra-small fw-bold text-muted text-uppercase mb-1">Bukti Foto:</p>
                                            <img src="{{ asset('storage/' . $activeBanding->foto) }}" class="w-100 rounded border admin-lightbox" style="cursor: pointer; max-height: 150px; object-fit: cover;">
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('petugas.temuan.banding.proses', $activeBanding->id_pengajuan) }}" method="POST">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="status" value="disetujui" class="btn btn-success btn-sm flex-grow-1 fw-bold">SETUJUI BANDING</button>
                                            <button type="submit" name="status" value="ditolak" class="btn btn-outline-danger btn-sm flex-grow-1 fw-bold">TOLAK</button>
                                        </div>
                                    </form>
                                    <small class="text-muted d-block mt-2" style="font-size: 0.65rem;">* Jika setuju, Anda dapat mengubah deadline di bawah.</small>
                                </div>
                            </div>
                        @endif

                        {{-- 2. VERIFIKASI & DEADLINE --}}
                        <div class="card-body p-0">
                            @php
                                $isLocked = ($isVerified && $barangTemuan->deadline_penyerahan);
                                $hasApprovedBanding = $barangTemuan->pengajuanBanding()->where('status', 'disetujui')->exists();
                                $canEdit = !$isLocked || $hasApprovedBanding;
                            @endphp

                            @if($isLocked && !$hasApprovedBanding)
                                <div class="alert alert-secondary border-0 small py-2 fw-bold text-center mb-3">
                                    <i class="bi bi-lock-fill me-1"></i> STATUS & DEADLINE TERKUNCI
                                </div>
                            @endif

                            <form action="{{ route('petugas.temuan.update', $barangTemuan->id_barang) }}" method="POST" id="form-admin-main">
                                @csrf @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Status Verifikasi</label>
                                    <select class="form-select form-select-sm fw-bold border-2" name="status_verifikasi" required style="border-radius: 8px;" {{ !$canEdit ? 'disabled' : '' }}>
                                        <option value="belum" {{ $barangTemuan->status_verifikasi == 'belum' ? 'selected' : '' }}>PENDING</option>
                                        <option value="terverifikasi" {{ $barangTemuan->status_verifikasi == 'terverifikasi' ? 'selected' : '' }}>SETUJUI / VALID</option>
                                        <option value="ditolak" {{ $barangTemuan->status_verifikasi == 'ditolak' ? 'selected' : '' }}>TOLAK / ARSIP</option>
                                    </select>
                                    @if(!$canEdit)
                                        <input type="hidden" name="status_verifikasi" value="{{ $barangTemuan->status_verifikasi }}">
                                    @endif
                                </div>

                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                    <label class="small fw-bold text-primary text-uppercase mb-2 d-block" style="font-size: 0.65rem;">
                                        <i class="bi bi-clock-history me-1"></i> Batas Waktu Penyerahan
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <input type="date" name="deadline_tanggal" id="deadline_tanggal" class="form-control form-control-sm border-2 fw-semibold" 
                                                   min="{{ date('Y-m-d') }}"
                                                   value="{{ $barangTemuan->deadline_penyerahan ? $barangTemuan->deadline_penyerahan->format('Y-m-d') : date('Y-m-d') }}"
                                                   {{ !$canEdit ? 'readonly' : '' }}>
                                        </div>
                                        <div class="col-5">
                                            <input type="time" name="deadline_jam" id="deadline_jam" class="form-control form-control-sm border-2 fw-semibold" 
                                                   value="{{ $barangTemuan->deadline_penyerahan ? $barangTemuan->deadline_penyerahan->format('H:i') : date('H:i') }}"
                                                   {{ !$canEdit ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    
                                    @if($barangTemuan->deadline_penyerahan)
                                        @php
                                            $now = now();
                                            $deadline = $barangTemuan->deadline_penyerahan;
                                            $diff = $now->diff($deadline);
                                            $isLate = $now > $deadline;
                                            
                                            $timeLeft = "";
                                            if ($diff->d > 0) $timeLeft .= $diff->d . ' hari ';
                                            if ($diff->h > 0) $timeLeft .= $diff->h . ' jam ';
                                            if ($diff->i > 0) $timeLeft .= $diff->i . ' mnt';
                                            if (trim($timeLeft) == "") $timeLeft = "Kurang dari 1 mnt";
                                        @endphp
                                        <div class="mt-2 extra-small {{ $isLate ? 'text-danger' : 'text-primary' }} fw-bold">
                                            <i class="bi bi-hourglass-split"></i> 
                                            {{ $isLate ? 'TERLEWAT: ' : 'SISA WAKTU: ' }}{{ strtoupper($timeLeft) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-check form-switch mb-3 p-0 ps-5 mt-4">
                                    <input class="form-check-input" type="checkbox" id="status_penyerahan" name="status_penyerahan" value="sudah_diterima">
                                    <label class="form-check-label fw-bold text-primary" for="status_penyerahan">Klik Jika Barang Sudah Diterima</label>
                                </div>

                                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm" 
                                        onclick="confirmAction('form-admin-main', 'Update Laporan?', 'Perubahan data dan status akan disimpan.')"
                                        {{ $isReceived ? 'disabled' : '' }}>
                                    @if($isReceived)
                                        SUDAH DITERIMA
                                    @elseif(!$canEdit)
                                        KONFIRMASI PENERIMAAN
                                    @else
                                        SIMPAN PERUBAHAN
                                    @endif
                                </button>
                                @if(!$canEdit && !$isReceived)
                                    <div class="mt-2 text-center text-muted extra-small">
                                        Mahasiswa harus mengajukan <strong>Banding</strong> jika ingin tenggat waktu diubah.
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif
                @endif
            </div>

            @if(!$isSelesai && !$isReceived && !$isPenipuan && !$isVerified && \Auth::user()->role === 'admin')
            <div class="p-3 bg-light border-top text-center mt-3">
                <a href="#" class="text-danger small fw-bold text-decoration-none" onclick="if(confirm('Tandai sebagai penipuan? Poin pelapor akan dikurangi.')) document.getElementById('form-penipuan').submit();">
                    <i class="bi bi-slash-circle me-1"></i> Tandai Penipuan
                </a>
                <form id="form-penipuan" action="{{ route('petugas.temuan.penipuan', $barangTemuan->id_barang) }}" method="POST" class="d-none">@csrf</form>
            </div>
            @endif
        </div>

        @if($barangTemuan->status_penyerahan == 'sudah_diterima' && !$isSelesai)
            <div class="mt-3 p-3 bg-white rounded-4 border border-success border-opacity-25 shadow-sm" style="border-radius: 20px;">
                <div class="d-flex align-items-center gap-2 text-success mb-1">
                    <i class="bi bi-info-circle-fill"></i>
                    <span class="small fw-bold">Tahap Selanjutnya</span>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Barang sudah diamankan. Mahasiswa kini bisa mengajukan <strong>Klaim Barang</strong> untuk verifikasi kepemilikan akhir.</p>
            </div>
        @endif
    </div>
</div>

<!-- Lighbox Modal for Admin -->
<div class="modal fade" id="adminLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img src="" id="lightboxImg" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    @if($barangTemuan->latitude && $barangTemuan->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $barangTemuan->latitude }};
        const lng = {{ $barangTemuan->longitude }};
        const map = L.map('map-admin', { scrollWheelZoom: false }).setView([lat, lng], 17);
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 21 }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('<b>Lokasi Ditemukan</b>').openPopup();
    });
    @endif

    // Dynamic Time Validation
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('deadline_tanggal');
        const timeInput = document.getElementById('deadline_jam');

        function updateMinTime() {
            // Selalu ambil waktu sekarang saat fungsi dijalankan
            const now = new Date();
            const selectedDate = dateInput.value;
            const today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');

            if (selectedDate === today) {
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const minTime = `${hours}:${minutes}`;
                
                timeInput.min = minTime;
                
                // Jika jam yang dipilih ternyata sudah terlewat, paksa ke jam sekarang
                if (timeInput.value < minTime) {
                    timeInput.value = minTime;
                }
            } else {
                timeInput.removeAttribute('min');
            }
        }

        if (dateInput && timeInput) {
            dateInput.addEventListener('change', updateMinTime);
            // Cek setiap kali input jam diklik untuk memastikan waktu terbaru
            timeInput.addEventListener('focus', updateMinTime);
            updateMinTime();
        }
    });

    // Lightbox Logic
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('adminLightbox'));
        const img = document.getElementById('lightboxImg');
        document.querySelectorAll('.admin-lightbox').forEach(el => {
            el.addEventListener('click', function() {
                img.src = this.src;
                modal.show();
            });
        });
    });
</script>
@endpush