@extends('layouts.front')
@section('title', 'Detail Kehilangan - ' . $laporanKehilangan->nama_barang)

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
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    @media (min-width: 768px) { .detail-grid { grid-template-columns: repeat(3, 1fr); } }
    
    .info-item { background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; transition: all 0.2s; }
    .info-item:hover { border-color: #0A4174; background: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .info-label { font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-value { font-size: 0.95rem; font-weight: 700; color: #0f172a; word-break: break-word; }
    
    .guide-box { background: #f0f7ff; border-radius: 20px; padding: 1.5rem; border: 1px solid #dbeafe; display: flex; gap: 1rem; align-items: flex-start; }
    .guide-icon { width: 44px; height: 44px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #0A4174; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    
    .btn-premium { border-radius: 50px; padding: 1rem 2rem; font-weight: 800; transition: all 0.3s; letter-spacing: 0.5px; border: none; }
    .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    #map-detail { height: 300px; width: 100%; border-radius: 20px; border: 1px solid #e2e8f0; margin-top: 15px; }
    
    .text-gradient { background: linear-gradient(135deg, #facc15 0%, #eab308 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto mb-5">
        {{-- BACK LINK --}}
        <div class="mb-4">
            <a href="{{ route('mahasiswa.kehilangan.index') }}" class="btn btn-link text-muted text-decoration-none p-0 small fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-circle-fill fs-5"></i> KEMBALI KE DAFTAR
            </a>
        </div>

        @php
            $status = $laporanKehilangan->status;
            $isOwner = $laporanKehilangan->id_user === \Auth::id();
            $statusConfig = [
                'menunggu' => ['color' => '#1e40af', 'text' => 'MENCARI BARANG', 'bg' => '#dbeafe', 'icon' => 'bi-search'],
                'diproses' => ['color' => '#1e3a8a', 'text' => 'DALAM PROSES', 'bg' => '#dbeafe', 'icon' => 'bi-hourglass-split'],
                'dicocokkan' => ['color' => '#3730a3', 'text' => 'SUDAH DITEMUKAN', 'bg' => '#e0e7ff', 'icon' => 'bi-check-all'],
                'selesai' => ['color' => '#065f46', 'text' => 'BARANG KEMBALI', 'bg' => '#d1fae5', 'icon' => 'bi-patch-check-fill'],
                'ditolak' => ['color' => '#991b1b', 'text' => 'DITUTUP / DITOLAK', 'bg' => '#fee2e2', 'icon' => 'bi-x-circle']
            ];
            $cfg = $statusConfig[$status] ?? $statusConfig['menunggu'];
        @endphp

        <div class="premium-card">
            {{-- NAVY HEADER --}}
            <div class="premium-header text-center">
                <div class="status-badge-top" style="background-color: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};">
                    <i class="bi {{ $cfg['icon'] }}"></i> {{ $cfg['text'] }}
                </div>
                <h1 class="display-6 fw-900 text-white mb-2 text-uppercase" style="letter-spacing: -1px;">{{ $laporanKehilangan->nama_barang }}</h1>
                <p class="mb-0 small fw-bold text-white-50 opacity-75">
                    ID LAPORAN: #H{{ $laporanKehilangan->id_laporan }} <span class="mx-2">|</span> 
                    DILAPORKAN: {{ $laporanKehilangan->created_at->format('d M Y') }}
                </p>
            </div>

            <div class="p-4 p-md-5">
                {{-- SECTION: MANAJEMEN LAPORAN & PROGRES --}}
                <div class="bg-light p-4 p-md-5 rounded-4 border mb-5">
                    <h6 class="section-title border-bottom pb-3 mb-4"><i class="bi bi-gear-fill me-2"></i> Manajemen Laporan & Progres</h6>
                    
                    @if($isOwner)
                        {{-- STEPPER: UNTUK PELAPOR --}}
                        <div class="sc-stepper mb-5 px-lg-5">
                            @php
                                $isDitinjau = in_array($status, ['diproses', 'dicocokkan', 'selesai']);
                                $isCocok = in_array($status, ['dicocokkan', 'selesai']);
                                $isSelesai = ($status == 'selesai');
                            @endphp
                            <div class="sc-step completed">
                                <div class="sc-step-icon"><i class="bi bi-check2"></i></div>
                                <div class="sc-step-label">Dilaporkan</div>
                            </div>
                            <div class="sc-step {{ $isDitinjau ? 'completed' : 'active' }}">
                                <div class="sc-step-icon">@if($isDitinjau)<i class="bi bi-check2"></i>@else 2 @endif</div>
                                <div class="sc-step-label">Ditinjau</div>
                            </div>
                            <div class="sc-step {{ $isCocok ? 'completed' : ($isDitinjau ? 'active' : '') }}">
                                <div class="sc-step-icon">@if($isCocok)<i class="bi bi-check2"></i>@else 3 @endif</div>
                                <div class="sc-step-label">Ditemukan</div>
                            </div>
                            <div class="sc-step {{ $isSelesai ? 'completed' : ($isCocok ? 'active' : '') }}">
                                <div class="sc-step-icon">@if($isSelesai)<i class="bi bi-check2"></i>@else 4 @endif</div>
                                <div class="sc-step-label">Selesai</div>
                            </div>
                        </div>
                    @else
                        {{-- PUBLIC VIEWER STATUS --}}
                        <div class="sc-status-box mb-4">
                            <div class="sc-status-label" style="background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }}; border-color: {{ $cfg['color'] }}33;">
                                {{ $cfg['text'] }}
                            </div>
                            <p class="mb-0 small fw-bold text-muted px-3">Status laporan ini diawasi oleh Admin PNP. Detail progres khusus tersedia bagi pemilik laporan.</p>
                        </div>
                    @endif

                    @if($isOwner || (!$isOwner && $status == 'menunggu'))
                    {{-- CONTEXTUAL ACTION GUIDE --}}
                    <div class="guide-box mb-0">
                        <div class="guide-icon"><i class="bi bi-lightbulb-fill"></i></div>
                        <div>
                            <h6 class="extra-small fw-800 text-uppercase mb-1" style="color: #64748b;">Panduan Langkah</h6>
                            <p class="mb-0 small fw-bold" style="color: #1e3a8a; line-height: 1.4;">
                                @if($status == 'menunggu')
                                    @if($isOwner)
                                        Laporan Anda sedang dipublikasikan. Tunggu hingga seseorang menemukan barang Anda di area kampus.
                                    @else
                                        Apakah Anda melihat atau memegang barang ini? Silakan klik tombol di bawah untuk melapor.
                                    @endif
                                @elseif($status == 'dicocokkan')
                                    @if($isOwner)
                                        Berita Bagus! Seseorang melaporkan telah menemukan barang Anda. Mohon tunggu proses verifikasi admin pusat.
                                    @endif
                                @elseif($status == 'selesai')
                                    @if($isOwner)
                                        Laporan ini sudah selesai. Barang telah kembali ke Anda melalui POB Smart Campus.
                                    @endif
                                @else
                                    @if($isOwner)
                                        {{ $status == 'ditolak' ? 'Laporan ini ditolak karena data tidak valid atau sudah kadaluarsa.' : 'Laporan sedang dalam pengawasan admin.' }}
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <h6 class="section-title border-bottom pb-3"><i class="bi bi-info-circle-fill text-primary"></i> Atribut & Detail Barang</h6>
                        <div class="detail-grid mt-4">
                             <div class="info-item">
                                <span class="info-label"><i class="bi bi-geo-alt"></i>Lokasi Kejadian</span>
                                <span class="info-value text-danger">{{ $laporanKehilangan->lokasi_hilang ?? 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-palette"></i>Warna Dominan</span>
                                <span class="info-value">{{ $laporanKehilangan->warna ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-event"></i>Tanggal Hilang</span>
                                <span class="info-value">{{ $laporanKehilangan->tanggal_hilang ? date('d M Y', strtotime($laporanKehilangan->tanggal_hilang)) : '-' }}</span>
                            </div>
                            
                            @if($laporanKehilangan->merk)
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-tag"></i>Merk / Brand</span>
                                <span class="info-value">{{ $laporanKehilangan->merk }}</span>
                            </div>
                            @endif
                            @if($laporanKehilangan->tipe_model)
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-layers"></i>Tipe / Model</span>
                                <span class="info-value">{{ $laporanKehilangan->tipe_model }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-card-text"></i>Kategori</span>
                                <span class="info-value">{{ $laporanKehilangan->kategori->nama_kategori }}</span>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-light rounded-4 border">
                            <span class="info-label mb-2"><i class="bi bi-chat-left-text"></i>Ciri Khusus & Deskripsi Tambahan</span>
                            <p class="mb-0 fw-medium text-dark" style="line-height: 1.6; font-size: 0.95rem;">{{ $laporanKehilangan->ciri_khusus }}</p>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="row g-4">
                            {{-- PHOTO SECTION --}}
                            @php $fotos = is_array($laporanKehilangan->foto) ? $laporanKehilangan->foto : json_decode($laporanKehilangan->foto, true); @endphp
                            @if($fotos && count($fotos) > 0)
                            <div class="col-md-{{ ($laporanKehilangan->latitude && $laporanKehilangan->longitude) ? '6' : '12' }}">
                                <h6 class="section-title border-bottom pb-3"><i class="bi bi-camera-fill text-primary"></i> Dokumentasi Terlampir</h6>
                                <div class="row g-2 mt-3">
                                    @foreach($fotos as $path)
                                    <div class="col-{{ ($laporanKehilangan->latitude && $laporanKehilangan->longitude) ? '6' : '4' }} col-md-{{ ($laporanKehilangan->latitude && $laporanKehilangan->longitude) ? '6' : '3' }}">
                                        <div class="rounded-4 border overflow-hidden shadow-sm bg-light">
                                            <img src="{{ asset('storage/' . $path) }}" class="w-100 img-lightbox" style="aspect-ratio: 1/1; object-fit: cover; cursor: pointer;" alt="Foto Barang">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- MAP SECTION --}}
                            @if($laporanKehilangan->latitude && $laporanKehilangan->longitude)
                            <div class="col-md-{{ ($fotos && count($fotos) > 0) ? '6' : '12' }}">
                                <h6 class="section-title border-bottom pb-3"><i class="bi bi-geo-fill text-primary"></i> Estimasi Titik Lokasi</h6>
                                <div id="map-detail" class="shadow-sm mt-3"></div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACTION BUTTONS SECTION --}}
                    <div class="col-12 mt-5 pt-5 border-top">
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 align-items-center">
                            @if(!$isOwner && in_array($status, ['menunggu', 'diproses']))
                                {{-- Link instead of Form for easier clickability --}}
                                <a href="{{ route('mahasiswa.kehilangan.markFound', $laporanKehilangan->id_laporan) }}" class="btn btn-primary btn-premium px-5 shadow-lg w-100 w-md-auto">
                                    <i class="bi bi-search me-2"></i> SAYA MENEMUKAN BARANG INI
                                </a>
                            @elseif($isOwner)
                                @if($status == 'menunggu')
                                    <form action="{{ route('mahasiswa.kehilangan.destroy', $laporanKehilangan->id_laporan) }}" method="POST" id="form-delete-kehilangan">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-premium px-5 w-100 w-md-auto" onclick="confirmAction('form-delete-kehilangan', 'Batalkan Laporan?', 'Apakah Anda yakin ingin membatalkan laporan kehilangan ini? Data akan dihapus permanen.', 'warning')">
                                            <i class="bi bi-trash3 me-2"></i> BATALKAN LAPORAN INI
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-light border-0 py-3 rounded-4 shadow-sm mb-0">
                                        <i class="bi bi-shield-lock-fill text-muted fs-4 d-block mb-2"></i>
                                        <h6 class="fw-bold mb-1">Laporan Terproteksi</h6>
                                        <p class="text-muted small mb-0 px-md-5">Laporan ini sudah masuk tahap penanganan atau pencocokan data. Anda tidak lagi dapat menghapus laporan ini untuk menjaga validitas proses pencarian.</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted extra-small fw-bold text-uppercase d-block mb-1">Update terakhir: {{ $laporanKehilangan->updated_at->diffForHumans() }}</small>
                            <small class="text-muted extra-small fw-bold text-uppercase">Sistem Lost & Found PNP</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    @if($laporanKehilangan->latitude && $laporanKehilangan->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $laporanKehilangan->latitude }};
        const lng = {{ $laporanKehilangan->longitude }};
        const map = L.map('map-detail', { scrollWheelZoom: false }).setView([lat, lng], 17);
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 21 }).addTo(map);

        var customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#0A4174; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 0 15px rgba(10,65,116,0.6);'></div>",
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });

        L.marker([lat, lng], {icon: customIcon}).addTo(map)
            .bindPopup('<div class="fw-bold">Lokasi Hilang</div><div class="small">{{ $laporanKehilangan->nama_barang }}</div>')
            .openPopup();
    });
    @endif
</script>
@endpush