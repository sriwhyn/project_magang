@extends('layouts.front')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    .premium-card { border: none; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,29,57,0.08); overflow: hidden; background: white; }
    .premium-header { 
        background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); 
        padding: 2.5rem 2rem; 
        position: relative;
    }
    .premium-header h1 { color: #ffffff !important; font-weight: 900; }
    .premium-header p { color: rgba(255,255,255,0.7) !important; font-weight: 600; }
    .status-badge-top {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.7rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .info-item { background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; transition: all 0.2s; height: 100%; }
    .info-item:hover { border-color: #0A4174; background: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .info-label { font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-value { font-size: 0.95rem; font-weight: 700; color: #0f172a; word-break: break-word; }
    
    .btn-action { border-radius: 12px; padding: 0.8rem 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: all 0.2s; border: none; }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    #map-detail { height: 350px; border-radius: 20px; border: 1px solid #e2e8f0; }
    .img-premium { width: 100%; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    
    .section-title { font-size: 0.85rem; font-weight: 900; color: #001D39; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 10px; }
    .sc-status-box { background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .sc-status-label { display: inline-block; padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 1rem; border: 1px solid; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="col-lg-10 mx-auto">
        {{-- Header & Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('mahasiswa.temuan.index') }}" class="btn btn-link text-muted text-decoration-none p-0 small fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-circle-fill fs-5"></i> KEMBALI
            </a>
            @if(\Auth::id() == $barangTemuan->id_user_pelapor && $barangTemuan->status_penyerahan == 'menunggu_diserahkan' && $barangTemuan->status_verifikasi != 'terverifikasi' && count($barangTemuan->klaimBarang->where('status_klaim', '!=', 'ditolak')) == 0)
                <form action="{{ route('mahasiswa.temuan.destroy', $barangTemuan->id_barang) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-trash me-1"></i> HAPUS</button>
                </form>
            @endif
        </div>

        <div class="premium-card">
            @php
                $isVerified = ($barangTemuan->status_verifikasi == 'terverifikasi');
                $isAtAdmin = ($barangTemuan->status_penyerahan == 'sudah_diterima');
                $isSelesai = ($barangTemuan->status_klaim == 'selesai');
                $isLate = ($barangTemuan->deadline_penyerahan && now()->gt($barangTemuan->deadline_penyerahan));
                $isReporter = (\Auth::id() == $barangTemuan->id_user_pelapor);
                $existingBanding = $barangTemuan->pengajuanBanding()->where('id_user', \Auth::id())->first();
                $isLocked = ($barangTemuan->status_verifikasi == 'terverifikasi' || $barangTemuan->status_penyerahan == 'sudah_diterima' || count($barangTemuan->klaimBarang->where('status_klaim', '!=', 'ditolak')) > 0);
                $isMatchedOwner = ($barangTemuan->id_laporan_kehilangan && $barangTemuan->laporanKehilangan && $barangTemuan->laporanKehilangan->id_user === \Auth::id());
                $myClaim = $barangTemuan->klaimBarang->where('id_user_pengaju', \Auth::id())->first();
            @endphp

            {{-- Navy Banner --}}
            <div class="premium-header text-center">
                <div class="status-badge-top bg-white text-primary">
                    <i class="bi bi-tag-fill"></i> #T{{ $barangTemuan->id_barang }} <span class="mx-2">|</span> {{ strtoupper($barangTemuan->status_verifikasi) }}
                </div>
                <h1 class="display-6 fw-900 text-white mb-2 text-uppercase" style="letter-spacing: -1px;">{{ $barangTemuan->nama_barang }}</h1>
                <p class="mb-0 small fw-bold text-white-50 opacity-75">
                    DITEMUKAN PADA: {{ $barangTemuan->tanggal_ditemukan->format('d M Y') }} <span class="mx-2">|</span>
                    STATUS FISIK: {{ $barangTemuan->status_penyerahan == 'sudah_diterima' ? 'DI KANTOR ADMIN' : 'MENUNGGU DISERAHKAN' }}
                </p>
            </div>

            <div class="p-4 p-md-5">
                <div class="row g-4 mb-5">
                    <div class="col-md-8">
                        <h6 class="section-title"><i class="bi bi-info-circle-fill text-primary"></i> Atribut & Detail Barang</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-geo-alt"></i>Lokasi Ditemukan</span>
                                    <span class="info-value text-primary">{{ $barangTemuan->lokasi_nama }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-tag"></i>Kategori</span>
                                    <span class="info-value">{{ $barangTemuan->kategori->nama_kategori }}</span>
                                </div>
                            </div>
                            @if($barangTemuan->merk)
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-layers"></i>Merk / Brand</span>
                                    <span class="info-value">{{ $barangTemuan->merk }}</span>
                                </div>
                            </div>
                            @endif
                            @if($barangTemuan->warna)
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-palette"></i>Warna Utama</span>
                                    <span class="info-value">{{ $barangTemuan->warna }}</span>
                                </div>
                            </div>
                            @endif
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-chat-left-text"></i>Deskripsi Singkat</span>
                                    <p class="mb-0 info-value fw-medium" style="line-height:1.5;">{{ $barangTemuan->deskripsi_singkat }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action for Claimer --}}
                        @if(!$isReporter && $isAtAdmin && !$isSelesai)
                            @if($isMatchedOwner)
                                {{-- Jika user adalah pemilik yang dicocokkan --}}
                                <div class="bg-success bg-opacity-10 p-4 rounded-4 border border-success border-opacity-25 mt-4 text-center">
                                    <div class="badge bg-success rounded-pill px-3 py-2 mb-3 shadow-sm">
                                        <i class="bi bi-patch-check-fill me-1"></i> MATCH FOUND!
                                    </div>
                                    <p class="fw-bold text-success mb-3 small">Barang ini telah dicocokkan dengan laporan kehilangan Anda (#H{{ $barangTemuan->id_laporan_kehilangan }}).</p>
                                    @if($myClaim)
                                        <a href="{{ route('mahasiswa.klaim.show', $myClaim->id_klaim) }}" class="btn btn-success btn-lg px-5 rounded-pill fw-900 shadow-sm border-0">
                                            LIHAT PROGRES KLAIM SAYA
                                        </a>
                                    @else
                                        <a href="{{ route('mahasiswa.klaim.create', ['id_barang' => $barangTemuan->id_barang]) }}" class="btn btn-success btn-lg px-5 rounded-pill fw-900 shadow-sm border-0">
                                            AKTIFKAN KLAIM SEKARANG
                                        </a>
                                    @endif
                                </div>
                            @elseif($myClaim)
                                {{-- Jika user sudah mengajukan klaim manual --}}
                                <div class="bg-primary bg-opacity-10 p-4 rounded-4 border border-primary border-opacity-25 mt-4 text-center">
                                    <p class="fw-bold text-primary mb-3 small">Anda telah mengajukan klaim untuk barang ini. Cek status verifikasi secara berkala.</p>
                                    <a href="{{ route('mahasiswa.klaim.show', $myClaim->id_klaim) }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-900 shadow-sm border-0">
                                        LIHAT PROGRES KLAIM SAYA
                                    </a>
                                </div>
                            @else
                                {{-- Jika belum klaim, tetap bisa klaim meskipun barang sudah ada klaim lain atau match dengan orang lain --}}
                                <div class="bg-primary bg-opacity-10 p-4 rounded-4 border border-primary border-opacity-25 mt-4 text-center">
                                    @if($barangTemuan->id_laporan_kehilangan || $barangTemuan->status_klaim == 'proses')
                                        <div class="alert alert-warning border-0 small fw-bold mb-3 rounded-3" style="background:#fff3cd; color:#856404;">
                                            <i class="bi bi-info-circle-fill me-2"></i> Barang ini sedang dalam proses review klaim/kecocokan laporan. Anda tetap bisa mengajukan klaim jika merasa ini milik Anda.
                                        </div>
                                    @else
                                        <p class="fw-bold text-primary mb-3 small">Ini adalah barang milik Anda? Silakan ajukan klaim untuk verifikasi pengambilan.</p>
                                    @endif
                                    <a href="{{ route('mahasiswa.klaim.create', ['id_barang' => $barangTemuan->id_barang]) }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-900 shadow-sm border-0">
                                        AJUKAN KLAIM SEKARANG
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="col-md-4">
                        <h6 class="section-title"><i class="bi bi-camera-fill text-primary"></i> Dokumentasi Foto</h6>
                        @php 
                            $canSeePhoto = \Auth::check() && (\Auth::user()->role == 'admin' || \Auth::user()->role == 'petugas' || \Auth::id() == $barangTemuan->id_user_pelapor);
                            $fotos = is_array($barangTemuan->foto) ? $barangTemuan->foto : json_decode($barangTemuan->foto, true);
                        @endphp
                        @if($canSeePhoto && $fotos && count($fotos) > 0)
                            <div class="row g-2">
                                @foreach($fotos as $path)
                                    <div class="col-6 col-md-12">
                                        <img src="{{ asset('storage/' . $path) }}" class="img-premium mb-2 img-lightbox" style="aspect-ratio: 4/3; object-fit: cover; cursor: pointer;" alt="Foto Barang">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-5 border border-dashed rounded-4 bg-light">
                                <i class="bi bi-shield-lock-fill text-muted display-6 mb-3 opacity-25"></i>
                                <p class="extra-small text-muted fw-bold mb-0 text-uppercase">Fisik barang hanya dapat dilihat oleh pelapor & admin untuk keamanan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light p-4 p-md-5 rounded-4 border">
                            <h6 class="section-title border-bottom pb-3"><i class="bi bi-gear-fill me-2"></i> Manajemen Laporan & Progres</h6>

                            {{-- Standardized Stepper: HANYA untuk pelapor --}}
                            @if($isReporter)
                            <div class="sc-stepper mb-5 px-lg-5">
                                <div class="sc-step completed">
                                    <div class="sc-step-icon"><i class="bi bi-check2"></i></div>
                                    <div class="sc-step-label">Dilaporkan</div>
                                </div>
                                <div class="sc-step {{ $isVerified || $isAtAdmin || $isSelesai ? 'completed' : 'active' }}">
                                    <div class="sc-step-icon">
                                        @if($isVerified || $isAtAdmin || $isSelesai) <i class="bi bi-check2"></i> @else 2 @endif
                                    </div>
                                    <div class="sc-step-label">Diverifikasi</div>
                                </div>
                                <div class="sc-step {{ $isAtAdmin || $isSelesai ? 'completed' : ($isVerified ? 'active' : '') }}">
                                    <div class="sc-step-icon">
                                        @if($isAtAdmin || $isSelesai) <i class="bi bi-check2"></i> @else 3 @endif
                                    </div>
                                    <div class="sc-step-label">Serah Terima</div>
                                </div>
                                <div class="sc-step {{ $isSelesai ? 'completed' : ($isAtAdmin ? 'active' : '') }}">
                                    <div class="sc-step-icon">
                                        @if($isSelesai) <i class="bi bi-check2"></i> @else 4 @endif
                                    </div>
                                    <div class="sc-step-label">Selesai</div>
                                </div>
                            </div>
                            @else
                                {{-- PUBLIC VIEWER STATUS --}}
                                <div class="sc-status-box mb-4">
                                    <div class="sc-status-label" style="background: {{ $isSelesai ? '#ecfdf5' : '#eff6ff' }}; color: {{ $isSelesai ? '#059669' : '#0A4174' }}; border-color: {{ $isSelesai ? '#059669' : '#0A4174' }}33;">
                                        {{ $isSelesai ? 'SUDAH DIAMBIL' : ($isAtAdmin ? 'DI KANTOR ADMIN' : 'MENUNGGU VERIFIKASI') }}
                                    </div>
                                    <p class="mb-0 small fw-bold text-muted px-4">
                                        @if($isSelesai)
                                            Barang ini telah diklaim dan diambil oleh pemiliknya yang sah.
                                        @elseif($isAtAdmin)
                                            Barang sudah diamankan di Kantor Admin. Silakan ajukan klaim jika ini milik Anda.
                                        @else
                                            Detail koordinasi penyerahan dan klaim hanya dapat diakses oleh pihak yang berkepentingan langsung.
                                        @endif
                                    </p>
                                </div>
                            @endif

                            {{-- Deadline & Warning Info --}}
                            @if($isVerified && !$isAtAdmin && $barangTemuan->deadline_penyerahan)
                                <div class="p-4 bg-white border border-2 rounded-4 {{ $isLate ? 'border-danger' : 'border-primary' }} shadow-sm">
                                    @if($isLate)
                                        @if(!$existingBanding)
                                            <div class="text-center mb-4">
                                                <div class="text-danger fw-900 fs-4 mb-2">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>TERLAMBAT!
                                                </div>
                                                <p class="mb-0 small fw-bold text-muted text-uppercase">Batas waktu penyerahan fisik ({{ $barangTemuan->deadline_penyerahan->format('d/m/Y H:i') }}) telah berakhir.</p>
                                            </div>

                                            @if($isReporter)
                                            <div class="border-top pt-4">
                                                <h6 class="fw-bold mb-3 text-dark small"><i class="bi bi-send-plus-fill me-2"></i>AJUKAN BANDING KETERLAMBATAN</h6>
                                                <form action="{{ route('mahasiswa.temuan.banding.submit', $barangTemuan->id_barang) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="extra-small fw-800 text-muted text-uppercase mb-1">Alasan / Kendala Utama</label>
                                                        <textarea name="alasan" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="3" placeholder="Jelaskan alasan keterlambatan Anda secara jujur..." required></textarea>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="extra-small fw-800 text-muted text-uppercase mb-1 d-block">Bukti Foto / Lampiran (Opsional)</label>
                                                        <div class="p-3 border-dashed rounded-4 bg-light text-center">
                                                            <input type="file" name="foto" class="form-control form-control-sm border-0 bg-transparent shadow-none" accept="image/*">
                                                            <small class="text-muted extra-small">Format: JPG, PNG, JPEG. Max: 2MB</small>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-900 py-3 shadow-sm border-0">
                                                        KIRIM PENGAJUAN BANDING
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        @else
                                            <div class="text-center py-2">
                                                <div class="badge {{ $existingBanding->status == 'ditolak' ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-4 py-2 fw-800 mb-2">
                                                    BANDING: {{ strtoupper($existingBanding->status) }}
                                                </div>
                                                <p class="mb-0 extra-small fw-bold text-muted">
                                                    Alasan: "{{ $existingBanding->alasan }}"
                                                </p>
                                                @if($existingBanding->status == 'disetujui')
                                                    <div class="alert alert-success mt-3 mb-0 py-2 small fw-bold">Admin menyetujui banding Anda. Menunggu pembaruan deadline.</div>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-3">
                                            <div class="text-primary fw-900 mb-1 small text-uppercase" style="letter-spacing:1px;">
                                                <i class="bi bi-clock-fill me-2"></i>WAKTU TERSISA PENYERAHAN
                                            </div>
                                            <h2 class="fw-900 text-dark mb-2">{{ strtoupper(now()->diffForHumans($barangTemuan->deadline_penyerahan, true)) }}</h2>
                                            <p class="mb-0 extra-small fw-bold text-muted">
                                                SEGERA SERAHKAN BARANG KE ADMIN SEBELUM <span class="text-primary">{{ $barangTemuan->deadline_penyerahan->format('d/m/Y H:i') }}</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @elseif($isVerified && $isAtAdmin)
                                <div class="p-4 bg-white border border-success rounded-4 text-center border-2 shadow-sm">
                                    <div class="text-success fw-900 mb-1 text-uppercase" style="letter-spacing: 1px;">
                                        <i class="bi bi-patch-check-fill me-2"></i>Barang Sudah Aman
                                    </div>
                                    @if($isReporter)
                                        <small class="text-muted fw-bold">Terima kasih! Barang telah Anda serahkan ke Admin. Anda sangat membantu pemilik barang ini.</small>
                                    @else
                                        <small class="text-muted fw-bold">Barang telah diamankan di Kantor Admin &bull; Siap untuk diproses pengambilan/klaim.</small>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4 bg-white rounded-4 border border-dashed">
                                    <span class="badge bg-secondary bg-opacity-10 text-muted px-4 py-2 rounded-pill fw-800">MENUNGGU KONFIRMASI / VERIFIKASI ADMIN</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACTION BUTTONS SECTION --}}
                    @if(\Auth::id() == $barangTemuan->id_user_pelapor)
                    <div class="col-12 mt-5 pt-5 border-top text-center">
                        @if($isLocked)
                            <div class="alert alert-light border-0 py-3 rounded-4 shadow-sm mb-4">
                                <i class="bi bi-shield-lock-fill text-muted fs-4 d-block mb-2"></i>
                                <h6 class="fw-bold mb-1">Laporan Terproteksi</h6>
                                <p class="text-muted small mb-0 px-md-5">Laporan ini sudah masuk ke sistem administrasi atau dalam proses klaim. Anda tidak lagi dapat menghapus laporan ini untuk menjaga integritas data.</p>
                            </div>
                        @else
                            <form action="{{ route('mahasiswa.temuan.destroy', $barangTemuan->id_barang) }}" method="POST" id="form-delete-temuan">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-outline-danger btn-premium px-5 w-100 w-md-auto" onclick="confirmAction('form-delete-temuan', 'Hapus Laporan?', 'Apakah Anda yakin ingin menghapus laporan temuan ini secara permanen?', 'warning')">
                                    <i class="bi bi-trash3 me-2"></i> HAPUS LAPORAN INI
                                </button>
                            </form>
                        @endif
                    </div>
                    @endif

                    <div class="col-12 mt-4 text-center">
                        @if($barangTemuan->latitude && $barangTemuan->longitude)
                            <h6 class="section-title justify-content-center mb-3"><i class="bi bi-geo-alt-fill text-primary"></i> Titik Estimasi Penemuan</h6>
                            <div id="map-detail" class="shadow-sm"></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light p-4 text-center border-top">
                <small class="text-muted fw-bold text-uppercase letter-spacing-1">Sistem Lost & Found PNP &bull; Laporan Terakhir: {{ $barangTemuan->updated_at->format('d M Y H:i') }}</small>
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
        const map = L.map('map-detail', { scrollWheelZoom: false }).setView([lat, lng], 17);
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 21 }).addTo(map);

        var customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#0A4174; width:16px; height:16px; border-radius:50%; border:2px solid white; box-shadow:0 0 15px rgba(10,65,116,0.6);'></div>",
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });

        L.marker([lat, lng], {icon: customIcon}).addTo(map)
            .bindPopup('<div class="fw-bold">Titik Penemuan</div><div class="small">{{ $barangTemuan->nama_barang }}</div>')
            .openPopup();
    });
    @endif
</script>
@endpush