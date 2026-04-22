@extends('layouts.front')
@section('title', 'Lapor Barang Temuan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    .premium-header { background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); color: white; padding: 2.5rem; border-radius: 12px 12px 0 0; }
    .premium-section-title { font-size: 0.75rem; font-weight: 800; color: #001D39; text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 6px; margin-bottom: 20px; margin-top: 25px; }
    #map { height: 280px; width: 100%; border-radius: 12px; border: 2px solid #cbd5e1; z-index: 1; }
    .form-control-premium { border: 2px solid #cbd5e1; padding: 0.75rem 1rem; font-size: 0.85rem; border-radius: 10px; color: #011627; font-weight: 500; }
    .form-control-premium:focus { border-color: #0A4174; box-shadow: 0 4px 12px rgba(10, 65, 116, 0.1); }
    .form-label-premium { font-size: 0.8rem; font-weight: 800; color: #001D39; text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 0.5px; }
    .category-section { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .btn-premium { border-radius: 50px; padding: 1.1rem 2.2rem; font-weight: 800; transition: all 0.3s; background: #001D39; border: none; letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.85rem; }
    .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0, 29, 57, 0.2); background: #0A4174; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-7 mx-auto mb-5">
        {{-- BACK LINK --}}
        <div class="mb-3 text-start">
            <a href="{{ route('mahasiswa.temuan.index') }}" class="btn btn-link text-muted text-decoration-none p-0 extra-small fw-bold">
                <i class="bi bi-chevron-left"></i> KEMBALI KE DAFTAR
            </a>
        </div>

        {{-- MAIN FORM CARD --}}
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
            <div class="premium-header text-center">
                <h4 class="fw-800 mb-1" style="color: #facc15; text-shadow: 0 2px 4px rgba(0,0,0,0.2); letter-spacing: 1px;">LAPOR PENEMUAN BARANG</h4>
                <p class="mb-0 extra-small opacity-75 fw-medium">Bantu mengembalikan barang kepada pemiliknya yang sah.</p>
            </div>

            <div class="card-body p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger extra-small rounded-3 mb-4">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                @if(isset($prefilled['id_laporan_kehilangan']))
                    <div class="alert alert-primary rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-3">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;flex-shrink:0;">
                            <i class="bi bi-magic text-primary"></i>
                        </div>
                        <div class="extra-small fw-bold text-primary">Formulir telah diisi otomatis berdasarkan Laporan Kehilangan terkait. Silakan lengkapi sisa datanya.</div>
                    </div>
                @endif

                <form action="{{ route('mahasiswa.temuan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($prefilled['id_laporan_kehilangan']))
                        <input type="hidden" name="id_laporan_kehilangan" value="{{ $prefilled['id_laporan_kehilangan'] }}">
                    @endif
                    
                    <div class="premium-section-title">1. Identitas Barang</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label-premium">Apa yang Ditemukan? <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-premium" name="nama_barang" value="{{ old('nama_barang', $prefilled['nama_barang'] ?? '') }}" required placeholder="Contoh: Kunci Motor Honda, Laptop Asus">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label-premium">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select form-control-premium" id="id_kategori" name="id_kategori" required>
                                <option value="">-- Pilih --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori', $prefilled['id_kategori'] ?? '') == $kategori->id_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-premium">Merk / Brand</label>
                            <input type="text" class="form-control form-control-premium" name="merk" value="{{ old('merk', $prefilled['merk'] ?? '') }}" placeholder="Contoh: Honda, Samsung, Kenko, Eiger">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">Tipe / Model</label>
                            <input type="text" class="form-control form-control-premium" name="tipe_model" value="{{ old('tipe_model', $prefilled['tipe_model'] ?? '') }}" placeholder="Contoh: Vario 150, Galaxy S21, A4 80gsm">
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-3">
                        <div class="col-md-6"><label class="form-label-premium">Tanggal Penemuan <span class="text-danger">*</span></label><input type="date" class="form-control form-control-premium" name="tanggal_ditemukan" value="{{ old('tanggal_ditemukan', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required></div>
                        <div class="col-md-6"><label class="form-label-premium">Warna Dominan <span class="text-danger">*</span></label><input type="text" class="form-control form-control-premium" name="warna" value="{{ old('warna', $prefilled['warna'] ?? '') }}" required placeholder="Hitam, Perak, Merah, dsb"></div>
                    </div>

                    {{-- DYNAMIC FIELDS --}}
                    <div id="dynamic-fields" class="mt-3">
                        {{-- KENDARAAN --}}
                        <div class="category-section d-none" id="section-kendaraan">
                            <div class="row g-2">
                                <div class="col-12"><label class="form-label-premium">Nomor Plat Kendaraan</label><input type="text" class="form-control form-control-premium" name="nomor_seri" value="{{ old('nomor_seri', $prefilled['nomor_seri'] ?? '') }}" placeholder="Contoh: BA 1234 ABC"></div>
                            </div>
                        </div>
                        {{-- SURAT / DOKUMEN --}}
                        <div class="category-section d-none" id="section-surat">
                            <div class="row g-2">
                                <div class="col-12"><label class="form-label-premium">Nomor Identitas / Dokumen</label><input type="text" class="form-control form-control-premium" name="nomor_seri" value="{{ old('nomor_seri', $prefilled['nomor_seri'] ?? '') }}" placeholder="Contoh: No. KTP / No. KTM"></div>
                            </div>
                        </div>
                        {{-- ELEKTRONIK --}}
                        <div class="category-section d-none" id="section-elektronik">
                            <div class="row g-2">
                                <div class="col-12"><label class="form-label-premium">IMEI / Serial Number</label><input type="text" class="form-control form-control-premium" name="nomor_seri" value="{{ old('nomor_seri', $prefilled['nomor_seri'] ?? '') }}" placeholder="No Seri atau IMEI perangkat"></div>
                            </div>
                        </div>

                    </div>

                    <div class="premium-section-title">2. Lokasi & Deskripsi</div>
                    <div class="mb-4">
                        <label class="form-label-premium">Titik Penemuan Gmaps (Opsional)</label>
                        <div id="map" class="mb-3"></div>
                        <input type="hidden" id="lat" name="latitude">
                        <input type="hidden" id="lng" name="longitude">
                        <small class="text-muted extra-small">Klik pada peta untuk menandai titik penemuan yang lebih akurat.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium">Nama Lokasi Penemuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-premium" name="lokasi_nama" value="{{ old('lokasi_nama') }}" required placeholder="Contoh: Depan Kantin Gedung C, Lantai 2 Musholla">
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label-premium">Deskripsi Penemuan <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-premium" name="deskripsi_singkat" rows="3" required placeholder="Sebutkan kondisi barang saat ditemukan atau info tambahan lainnya...">{{ old('deskripsi_singkat') }}</textarea>
                    </div>

                    <div class="mb-5 text-start">
                        <label class="form-label-premium">Foto Barang Temuan (Tipe Gambar)</label>
                        <input type="file" class="form-control form-control-premium" name="foto[]" multiple accept="image/*">
                        <small class="text-muted extra-small d-block mt-1">Gunakan foto yang jelas agar pemilik dapat mengenalinya.</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-premium w-100 text-white shadow">
                        <i class="bi bi-box-seam me-2"></i> PUBLIKASIKAN LAPORAN TEMUAN
                    </button>
                    
                    <div class="mt-4 p-3 bg-light rounded-3 border-start border-success border-4">
                        <p class="mb-0 extra-small text-muted" style="line-height: 1.5;">
                            <strong>Catatan:</strong> Setelah melapor, mohon simpan barang dengan baik. Admin akan memverifikasi laporan Anda sebelum mengarahkan serah terima barang.
                        </p>
                    </div>
                </form>
            </div>
            <div class="p-3 bg-light text-center extra-small text-muted border-top fw-bold">
                Sistem Lost & Found PNP
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Map Logic
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const map = L.map('map', { scrollWheelZoom: false }).setView([-0.9147, 100.4662], 16);
    L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 21 }).addTo(map);
    let marker;
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        latInput.value = e.latlng.lat;
        lngInput.value = e.latlng.lng;
    });

    // Category Logic
    const categorySelect = document.getElementById('id_kategori');
    const sections = document.querySelectorAll('.category-section');
    function updateFields() {
        const name = (categorySelect.options[categorySelect.selectedIndex]?.text || '').toLowerCase();
        sections.forEach(s => { s.classList.add('d-none'); });
        let active = document.getElementById('section-default');
        
        if (name.includes('kendaraan')) {
            active = document.getElementById('section-kendaraan');
        } else if (name.includes('surat') || name.includes('dokumen') || name.includes('kartu')) {
            active = document.getElementById('section-surat');
        } else if (name.includes('elektronik') || name.includes('laptop') || name.includes('hp') || name.includes('smartphone')) {
            active = document.getElementById('section-elektronik');
        }
        
        active?.classList.remove('d-none');
    }
    categorySelect.addEventListener('change', updateFields);
    if (categorySelect.value) updateFields();
});
</script>
@endpush
