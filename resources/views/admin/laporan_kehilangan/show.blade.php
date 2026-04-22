@extends(\Auth::user()->role === 'admin' ? 'layouts.app' : 'layouts.front')
@section('title', 'Detail Laporan - ' . $laporanKehilangan->nama_barang)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    .page-header-simple {
        background: white; border-radius: 16px; padding: 24px; border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;
    }
    .badge-status { padding: 6px 16px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .card-simple { background: white; border-radius: 20px; border: 1px solid rgba(0,0,0,0.05); height: 100%; overflow: hidden; }
    .label-section { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 1.25rem; display: block; }
    .info-list { width: 100%; }
    .info-list tr { border-bottom: 1px solid #f1f5f9; }
    .info-list tr:last-child { border-bottom: none; }
    .info-list th { padding: 14px 0; font-weight: 600; color: #64748b; font-size: 0.9rem; width: 40%; }
    .info-list td { padding: 14px 0; font-weight: 700; color: #1e293b; font-size: 0.9rem; text-align: right; }
    .photo-thumb { aspect-ratio: 1; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; cursor: zoom-in; transition: transform 0.2s; }
    .photo-thumb:hover { transform: scale(1.02); }
    #map-admin { height: 300px; width: 100%; border-radius: 16px; border: 1px solid #e2e8f0; z-index: 1; margin-top: 1rem; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('petugas.kehilangan.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="page-header-simple shadow-sm">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h4 class="fw-bold mb-0 text-dark">{{ $laporanKehilangan->nama_barang }}</h4>
            <span class="badge bg-light text-muted border">ID: #{{ $laporanKehilangan->id_laporan }}</span>
        </div>
        <div class="text-muted small"><i class="bi bi-person-circle me-1"></i> Pelapor: <span class="fw-bold text-primary">{{ $laporanKehilangan->user->email }}</span></div>
    </div>
    @php 
        $s = match($laporanKehilangan->status) { 
            'menunggu' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'Menunggu'], 
            'diproses' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'label' => 'Diproses'], 
            'selesai' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'Ditemukan'], 
            'ditolak' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Ditolak'], 
            'dicocokkan' => ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'label' => 'Dicocokkan'], 
            default => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => ucfirst($laporanKehilangan->status)] 
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
                <span class="label-section">Detail Informasi Lengkap</span>
                <table class="info-list">
                    <tr><th>Kategori</th><td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 rounded-pill">{{ $laporanKehilangan->kategori->nama_kategori }}</span></td></tr>
                    <tr><th>Tanggal Kejadian</th><td>{{ $laporanKehilangan->tanggal_hilang->format('d M Y') }}</td></tr>
                    <tr><th>Lokasi Terakhir</th><td>{{ $laporanKehilangan->lokasi_hilang }}</td></tr>
                    @if($laporanKehilangan->warna)<tr><th>Warna Barang</th><td>{{ $laporanKehilangan->warna }}</td></tr>@endif
                    @if($laporanKehilangan->merk)<tr><th>Merk / Brand</th><td>{{ $laporanKehilangan->merk }}</td></tr>@endif
                    @if($laporanKehilangan->tipe_model)<tr><th>Model / Jenis</th><td>{{ $laporanKehilangan->tipe_model }}</td></tr>@endif
                    @if($laporanKehilangan->ukuran)<tr><th>Ukuran</th><td class="text-primary">{{ $laporanKehilangan->ukuran }}</td></tr>@endif
                    @if($laporanKehilangan->nomor_seri)<tr><th>Nomor Seri / IMEI</th><td class="text-danger">{{ $laporanKehilangan->nomor_seri }}</td></tr>@endif
                </table>
            </div>

            <div class="mb-5">
                <span class="label-section">Deskripsi Kejadian</span>
                <div class="p-4 bg-light rounded-4 border-0 text-dark small" style="line-height: 1.7;">
                    {{ $laporanKehilangan->deskripsi }}
                </div>
            </div>

            @if($laporanKehilangan->ciri_khusus)
            <div class="mb-5">
                <span class="label-section">Ciri-ciri Khusus (Verifikasi Utama)</span>
                <div class="p-4 rounded-4" style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; font-weight: 600; font-size: 0.9rem;">
                    <i class="bi bi-shield-lock-fill me-2"></i> {{ $laporanKehilangan->ciri_khusus }}
                </div>
            </div>
            @endif

            @if($laporanKehilangan->latitude && $laporanKehilangan->longitude)
            <div class="mb-5">
                <span class="label-section">Koordinat Lokasi</span>
                <div id="map-admin"></div>
                <div class="mt-2 text-muted small"><i class="bi bi-geo-alt me-1"></i> {{ $laporanKehilangan->latitude }}, {{ $laporanKehilangan->longitude }}</div>
            </div>
            @endif

            @if($laporanKehilangan->foto)
            <div>
                <span class="label-section">Foto Barang Bukti</span>
                @php $fotos = is_array($laporanKehilangan->foto) ? $laporanKehilangan->foto : json_decode($laporanKehilangan->foto, true); @endphp
                @if($fotos && count($fotos) > 0)
                    <div class="row g-2">
                        @foreach($fotos as $path)
                            <div class="col-4 col-md-3">
                                <div class="photo-thumb shadow-sm">
                                    <img src="{{ asset('storage/' . $path) }}" class="w-100 h-100 object-fit-cover admin-lightbox" alt="Foto" style="cursor: zoom-in;">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-simple shadow-sm">
            <div class="p-3 border-bottom bg-light">
                <span class="label-section mb-0"><i class="bi bi-gear-fill me-1"></i> Panel Kontrol Admin</span>
            </div>
            
            <div class="p-3">
                {{-- Update Status --}}
                @if(\Auth::user()->role === 'admin')
                    @if($laporanKehilangan->status == 'selesai')
                        <div class="alert alert-success d-flex align-items-center mb-0 p-2" style="border-radius: 12px;">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div class="small fw-bold">Barang Sudah Ditemukan</div>
                        </div>
                    @else
                        <form action="{{ route('petugas.kehilangan.update', $laporanKehilangan->id_laporan) }}" method="POST" id="form-status">
                            @csrf @method('PUT')
                            <div class="mb-2">
                                <label class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Ganti Status Laporan</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-select fw-bold border-2" name="status" required style="border-radius: 8px 0 0 8px;">
                                        <option value="menunggu" {{ $laporanKehilangan->status == 'menunggu' ? 'selected' : '' }}>MENUNGGU</option>
                                        <option value="diproses" {{ $laporanKehilangan->status == 'diproses' ? 'selected' : '' }}>DIPROSES</option>
                                        <option value="selesai" {{ $laporanKehilangan->status == 'selesai' ? 'selected' : '' }}>DITEMUKAN</option>
                                        <option value="dicocokkan" {{ $laporanKehilangan->status == 'dicocokkan' ? 'selected' : '' }}>DICOCOKKAN</option>
                                        <option value="ditolak" {{ $laporanKehilangan->status == 'ditolak' ? 'selected' : '' }}>TOLAK / ARSIP</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary fw-bold">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                @else
                    <div class="p-2 bg-light rounded text-center">
                        <small class="text-muted fw-bold"><i class="bi bi-lock-fill me-1"></i> Admin Only</small>
                    </div>
                @endif

                {{-- Pencocokan Data --}}
                @if(\Auth::user()->role === 'admin' && $laporanKehilangan->status != 'ditemukan')
                    <div class="mt-3 pt-3 border-top">
                        <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Pencocokan Cerdas</label>
                        <a href="{{ route('petugas.temuan.index', ['kategori' => $laporanKehilangan->id_kategori]) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold">
                            <i class="bi bi-search me-1"></i> Cari Barang Temuan Mirip
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Info Tambahan atau Bantuan (Opsional) --}}
        <div class="mt-3 p-3 bg-white rounded-4 border border-info border-opacity-25" style="border-radius: 20px;">
            <div class="d-flex align-items-center gap-2 text-info mb-1">
                <i class="bi bi-info-circle-fill"></i>
                <span class="small fw-bold">Tips Verifikasi</span>
            </div>
            <p class="text-muted mb-0" style="font-size: 0.75rem;">Pastikan "Ciri Khusus" benar-benar cocok sebelum mengubah status menjadi <strong>Sudah Ditemukan</strong>.</p>
        </div>
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
    @if($laporanKehilangan->latitude && $laporanKehilangan->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $laporanKehilangan->latitude }};
        const lng = {{ $laporanKehilangan->longitude }};
        const map = L.map('map-admin', { scrollWheelZoom: false }).setView([lat, lng], 17);
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 21 }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('<b>Titik Kejadian</b><br>{{ $laporanKehilangan->lokasi_hilang }}').openPopup();
    });
    @endif

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