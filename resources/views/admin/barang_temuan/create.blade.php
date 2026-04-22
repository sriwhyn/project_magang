@extends(\Auth::user()->role === 'admin' ? 'layouts.app' : 'layouts.front')
@section('title', 'Tambah Barang Temuan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    #map { height: 400px; width: 100%; border-radius: 16px; z-index: 1; margin-bottom: 2rem; border: 1px solid rgba(0,0,0,0.05); }
    .category-section { transition: all 0.3s ease; }
    .category-section:not(.d-none) { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }

    .form-card-simple {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        padding: 40px;
    }

    .form-section-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-label::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    .form-group-custom {
        margin-bottom: 1.5rem;
    }

    .form-group-custom label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .input-simple {
        border-radius: 12px;
        border: 2px solid #f1f5f9;
        padding: 12px 16px;
        font-weight: 500;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .input-simple:focus {
        border-color: #7c3aed;
        background: white;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
    }

</style>
@endpush

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 class="fw-bold mb-1">Tambah Barang Temuan</h4>
        <p class="text-muted small mb-0">Masukkan data barang yang ditemukan untuk diproses lebih lanjut.</p>
    </div>
    <a href="{{ route('petugas.temuan.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Batal
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="form-card-simple shadow-sm">
            <form action="{{ route('petugas.temuan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-section-label">Informasi Dasar</div>
                <div class="row">
                    <div class="col-md-7 form-group-custom">
                        <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-simple" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Kunci Motor Honda">
                    </div>
                    <div class="col-md-5 form-group-custom">
                        <label for="id_kategori">Kategori Barang <span class="text-danger">*</span></label>
                        <select class="form-select input-simple" id="id_kategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group-custom mb-5">
                    <label for="id_laporan_kehilangan" class="text-primary"><i class="bi bi-link-45deg"></i> Hubungkan Laporan Kehilangan (Milik Mahasiswa / Opsional)</label>
                    <select class="form-select input-simple border-primary border-opacity-25" id="id_laporan_kehilangan" name="id_laporan_kehilangan">
                        <option value="">-- Pilih Jika Ini Adalah Jawaban Untuk Laporan Tertentu --</option>
                        @foreach($laporanKehilangan as $lost)
                            <option value="{{ $lost->id_laporan }}" {{ old('id_laporan_kehilangan') == $lost->id_laporan ? 'selected' : '' }}>
                                [#{{ $lost->id_laporan }}] {{ $lost->nama_barang }} - Pelapor: {{ $lost->user->email }} ({{ $lost->tanggal_hilang->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text small text-muted">Jika dipilih, sistem akan otomatis memberi tahu pemilik laporan bahwa barangnya telah ditemukan.</div>
                </div>

                <div id="dynamic-fields" class="mb-4">
                    <div class="category-section d-none" id="section-kendaraan">
                        <div class="row">
                            <div class="col-md-4 form-group-custom"><label>Merk</label><input type="text" class="form-control input-simple input-kendaraan" name="merk" value="{{ old('merk') }}" placeholder="Honda, Yamaha"></div>
                            <div class="col-md-4 form-group-custom"><label>Model / Tipe</label><input type="text" class="form-control input-simple input-kendaraan" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="Vario 125"></div>
                            <div class="col-md-4 form-group-custom"><label>No. Plat / Seri</label><input type="text" class="form-control input-simple input-kendaraan" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="BA 1234 XX"></div>
                        </div>
                    </div>
                    <div class="category-section d-none" id="section-surat">
                        <div class="row">
                            <div class="col-md-6 form-group-custom"><label>Jenis Dokumen</label><input type="text" class="form-control input-simple input-surat" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="KTP, KTM, SIM"></div>
                            <div class="col-md-6 form-group-custom"><label>Nomor Identitas</label><input type="text" class="form-control input-simple input-surat" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="NIK / NIM"></div>
                        </div>
                    </div>
                    <div class="category-section d-none" id="section-elektronik">
                        <div class="row">
                            <div class="col-md-4 form-group-custom"><label>Merk / Brand</label><input type="text" class="form-control input-simple input-elektronik" name="merk" value="{{ old('merk') }}" placeholder="Samsung, Apple"></div>
                            <div class="col-md-4 form-group-custom"><label>Model Perangkat</label><input type="text" class="form-control input-simple input-elektronik" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="Galaxy A54"></div>
                            <div class="col-md-4 form-group-custom"><label>IMEI / Serial No.</label><input type="text" class="form-control input-simple input-elektronik" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="S/N Perangkat"></div>
                        </div>
                    </div>
                    <div class="category-section d-none" id="section-pakaian">
                        <div class="row">
                            <div class="col-md-4 form-group-custom"><label>Merk / Label</label><input type="text" class="form-control input-simple input-pakaian" name="merk" value="{{ old('merk') }}" placeholder="Uniqlo, Nike"></div>
                            <div class="col-md-4 form-group-custom"><label>Ukuran</label><input type="text" class="form-control input-simple input-pakaian" name="ukuran" value="{{ old('ukuran') }}" placeholder="M, L, XL"></div>
                            <div class="col-md-4 form-group-custom"><label>Motif / Bahan</label><input type="text" class="form-control input-simple input-pakaian" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="Contoh: Polos, Katun"></div>
                        </div>
                    </div>
                    <div class="category-section d-none" id="section-default">
                        <div class="row">
                            <div class="col-md-4 form-group-custom"><label>Merk</label><input type="text" class="form-control input-simple input-default" name="merk" value="{{ old('merk') }}" placeholder="Brand"></div>
                            <div class="col-md-4 form-group-custom"><label>Tipe</label><input type="text" class="form-control input-simple input-default" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="Model / Tipe"></div>
                            <div class="col-md-4 form-group-custom"><label>No. Seri</label><input type="text" class="form-control input-simple input-default" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="Jika ada"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group-custom">
                        <label for="warna">Warna Dominan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-simple" id="warna" name="warna" value="{{ old('warna') }}" required placeholder="Contoh: Hitam">
                    </div>
                    <div class="col-md-6 form-group-custom">
                        <label for="tanggal_ditemukan">Tanggal Ditemukan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control input-simple" id="tanggal_ditemukan" name="tanggal_ditemukan" value="{{ old('tanggal_ditemukan', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-section-label mt-4">Lokasi Penemuan</div>
                <div class="alert alert-primary bg-primary bg-opacity-10 text-primary border-0 mb-4 py-3 rounded-4" style="font-size: 0.85rem;">
                    <i class="bi bi-geo-alt-fill me-2"></i> Klik pada peta di bawah ini untuk menandai lokasi tepat barang ditemukan.
                </div>
                <div id="map" class="shadow-sm"></div>
                <div class="row mb-4">
                    <div class="col-md-6 form-group-custom">
                        <label for="lokasi_nama">Nama Tempat / Gedung <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-simple" id="lokasi_nama" name="lokasi_nama" value="{{ old('lokasi_nama') }}" required placeholder="Contoh: Depan Mushalla PKM">
                    </div>
                    <div class="col-md-3 form-group-custom">
                        <label class="text-muted small">Latitude</label>
                        <input type="text" class="form-control input-simple bg-light" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                    </div>
                    <div class="col-md-3 form-group-custom">
                        <label class="text-muted small">Longitude</label>
                        <input type="text" class="form-control input-simple bg-light" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
                    </div>
                </div>

                <div class="form-section-label mt-4">Detail Tambahan</div>
                <div class="form-group-custom">
                    <label for="deskripsi_singkat">Deskripsi Singkat & Kondisi <span class="text-danger">*</span></label>
                    <textarea class="form-control input-simple" id="deskripsi_singkat" name="deskripsi_singkat" rows="4" required placeholder="Tuliskan kondisi barang saat ditemukan..."></textarea>
                </div>

                <div class="form-group-custom">
                    <label>Unggah Foto Barang</label>
                    <div class="p-4 border-2 border-dashed rounded-4 text-center bg-light" style="cursor: pointer;" onclick="document.getElementById('foto').click()">
                        <i class="bi bi-camera-fill display-6 text-muted mb-2 d-block"></i>
                        <span class="text-muted small fw-bold">Klik untuk pilih foto barang temuan</span>
                        <input type="file" id="foto" name="foto[]" accept="image/*" multiple class="d-none">
                    </div>
                    <div class="form-text mt-2 px-1 small">Maksimal: 20MB. Bisa pilih lebih dari satu foto.</div>
                </div>

                <div class="d-grid pt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm">Simpan Data Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pnpLat = -0.914561;
    const pnpLng = 100.466141;
    const map = L.map('map').setView([pnpLat, pnpLng], 17);
    L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 21 }).addTo(map);

    let marker;
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) { marker = L.marker([oldLat, oldLng]).addTo(map); map.setView([oldLat, oldLng], 18); }

    map.on('click', function(e) {
        document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
        if (marker) map.removeLayer(marker);
        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map).bindPopup('Lokasi Ditandai').openPopup();
    });

    const categorySelect = document.getElementById('id_kategori');
    const sections = document.querySelectorAll('.category-section');

    function updateDynamicFields() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const name = selectedOption ? selectedOption.text.toLowerCase() : '';
        sections.forEach(s => { s.classList.add('d-none'); s.querySelectorAll('input').forEach(i => i.disabled = true); });
        let active = document.getElementById('section-default');
        if (name.includes('kendaraan') || name.includes('motor') || name.includes('mobil')) active = document.getElementById('section-kendaraan');
        else if (name.includes('surat') || name.includes('dokumen') || name.includes('kartu')) active = document.getElementById('section-surat');
        else if (name.includes('elektronik') || name.includes('laptop') || name.includes('hp') || name.includes('gadget')) active = document.getElementById('section-elektronik');
        else if (name.includes('pakaian') || name.includes('baju') || name.includes('sepatu') || name.includes('tas')) active = document.getElementById('section-pakaian');
        if (active) { active.classList.remove('d-none'); active.querySelectorAll('input').forEach(i => i.disabled = false); }
    }

    categorySelect.addEventListener('change', updateDynamicFields);
    if (categorySelect.value) updateDynamicFields();
});
</script>
@endpush
