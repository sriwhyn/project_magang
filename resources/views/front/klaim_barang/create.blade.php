@extends('layouts.front')
@section('title', 'Ajukan Klaim Barang')

@push('styles')
<style>
    .premium-header { background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); color: white; padding: 2.5rem; border-radius: 12px 12px 0 0; }
    .premium-section-title { font-size: 0.75rem; font-weight: 800; color: #001D39; text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 6px; margin-bottom: 20px; margin-top: 25px; }
    .form-control-premium { border: 2px solid #cbd5e1; padding: 0.75rem 1rem; font-size: 0.85rem; border-radius: 10px; color: #011627; font-weight: 500; }
    .form-control-premium:focus { border-color: #0A4174; box-shadow: 0 4px 12px rgba(10, 65, 116, 0.1); }
    .form-label-premium { font-size: 0.8rem; font-weight: 800; color: #001D39; text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 0.5px; }
    .category-section { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .btn-premium { border-radius: 50px; padding: 1.1rem 2.2rem; font-weight: 800; transition: all 0.3s; background: #001D39; border: none; letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.85rem; color: white; }
    .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0, 29, 57, 0.2); background: #0A4174; color: white; }
</style>
@endpush

@section('content')
<div class="row justify-content-center mb-5 animate-fade">
    <div class="col-lg-8">
        <div class="mb-3 text-start">
            <a href="{{ url()->previous() }}" class="btn btn-link text-muted text-decoration-none p-0 extra-small fw-bold">
                <i class="bi bi-chevron-left"></i> KEMBALI
            </a>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
            <div class="premium-header text-center">
                <h4 class="fw-800 mb-1" style="color: #facc15; text-shadow: 0 2px 4px rgba(0,0,0,0.2); letter-spacing: 1px;">PENGAJUAN KLAIM</h4>
                <p class="mb-0 extra-small opacity-75 fw-medium">Verifikasi kepemilikan Anda untuk pengambilan barang.</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="p-4 rounded-4 mb-4 border-start border-4 border-warning shadow-sm" style="background: #fffbeb;">
                    <div class="d-flex gap-3">
                        <i class="bi bi-shield-lock-fill fs-4 text-warning"></i>
                        <div>
                            <h6 class="fw-900 mb-1" style="color: #856404; font-size: 0.85rem;">KEJUJURAN ADALAH ETIKA KAMPUS</h6>
                            <p class="mb-0 extra-small" style="color: #664d03; line-height: 1.5;">Sistem akan menilai kecocokan secara otomatis. Klaim palsu dapat berakibat investigasi serius dan penangguhan akses layanan.</p>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger extra-small rounded-3 mb-4">
                        <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('mahasiswa.klaim.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_barang" value="{{ $selectedBarang->id_barang }}">
                    <input type="hidden" name="id_kategori" value="{{ $selectedBarang->id_kategori }}">

                    <div class="premium-section-title">1. Informasi Barang</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label-premium">Barang yang Akan Diklaim</label>
                            <div class="form-control form-control-premium bg-light text-muted">
                                {{ $selectedBarang->tanggal_ditemukan->format('d M Y') }} | {{ $selectedBarang->nama_barang }} ({{ $selectedBarang->lokasi_nama }})
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label-premium">Kategori</label>
                            <div class="form-control form-control-premium bg-light text-muted">
                                {{ $selectedBarang->kategori->nama_kategori ?? '-' }}
                            </div>
                            <select class="d-none" id="id_kategori">
                                <option value="{{ $selectedBarang->id_kategori }}" selected>{{ $selectedBarang->kategori->nama_kategori ?? '-' }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="premium-section-title">2. Buktikan Kepemilikan Anda</div>
                    <div class="mb-4">
                        <label for="nama_pemilik" class="form-label-premium">Nama yang Tertera pada Barang (Jika ada) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-premium" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required placeholder="Contoh: Nama Anda, NIM, atau Nama Lain">
                    </div>

                    <div class="mb-4">
                        <label for="warna" class="form-label-premium">Warna Dominan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-premium" id="warna" name="warna" value="{{ old('warna') }}" required placeholder="Contoh: Merah, Hitam, Perak">
                    </div>

                    <div id="dynamic-fields" class="p-4 rounded-4 border bg-light mb-4">
                        <p class="extra-small text-muted mb-3 fw-bold text-uppercase"><i class="bi bi-info-circle-fill me-1"></i> Rincian Spesifik Kategori</p>

                        <div class="category-section d-none" id="section-surat">
                            <div class="row g-3">
                                <div class="col-md-6 text-start"><label class="form-label-premium">Jenis Surat/KTM</label><input type="text" class="form-control form-control-premium input-surat" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="KTP, KTM, SIM"></div>
                                <div class="col-md-6 text-start"><label class="form-label-premium">Nomor Identitas</label><input type="text" class="form-control form-control-premium input-surat" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="No. yang tertera"></div>
                            </div>
                        </div>
                        <div class="category-section d-none" id="section-kendaraan">
                            <div class="row g-3">
                                <div class="col-md-6 text-start"><label class="form-label-premium">Merk</label><input type="text" class="form-control form-control-premium input-kendaraan" name="merk" value="{{ old('merk') }}" placeholder="Contoh: Honda"></div>
                                <div class="col-md-6 text-start"><label class="form-label-premium">No. Plat</label><input type="text" class="form-control form-control-premium input-kendaraan" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="Contoh: BA 1234 XX"></div>
                            </div>
                        </div>
                        <div class="category-section d-none" id="section-elektronik">
                            <div class="row g-3">
                                <div class="col-md-4 text-start"><label class="form-label-premium">Merk</label><input type="text" class="form-control form-control-premium input-elektronik" name="merk" value="{{ old('merk') }}" placeholder="Samsung, Asus"></div>
                                <div class="col-md-4 text-start"><label class="form-label-premium">Model</label><input type="text" class="form-control form-control-premium input-elektronik" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="Galaxy A54"></div>
                                <div class="col-md-4 text-start"><label class="form-label-premium">IMEI/SN</label><input type="text" class="form-control form-control-premium input-elektronik" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="Serial Number"></div>
                            </div>
                        </div>
                        <div class="category-section" id="section-default">
                            <div class="row g-3">
                                <div class="col-md-6 text-start"><label class="form-label-premium">Merk</label><input type="text" class="form-control form-control-premium input-default" name="merk" value="{{ old('merk') }}" placeholder="Brand"></div>
                                <div class="col-md-6 text-start"><label class="form-label-premium">Tipe</label><input type="text" class="form-control form-control-premium input-default" name="tipe_model" value="{{ old('tipe_model') }}" placeholder="Jenis/Varian"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6 text-start">
                            <label for="tanggal_hilang" class="form-label-premium">Tanggal Kehilangan (Kira-kira) <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-premium" id="tanggal_hilang" name="tanggal_hilang" value="{{ old('tanggal_hilang') }}" required max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 text-start">
                            <label for="lokasi_hilang" class="form-label-premium">Prediksi Lokasi Jatuh <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-premium" id="lokasi_hilang" name="lokasi_hilang" value="{{ old('lokasi_hilang') }}" required placeholder="Contoh: Gedung D, Depan Musholla">
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <label for="isi_barang" class="form-label-premium">Isi Wadah (Jika berupa dompet/tas)</label>
                        <textarea class="form-control form-control-premium" id="isi_barang" name="isi_barang" rows="2" placeholder="Sebutkan benda-benda penting di dalamnya">{{ old('isi_barang') }}</textarea>
                    </div>

                    <div class="mb-4 text-start">
                        <label for="deskripsi_ciri_khusus" class="form-label-premium">Ciri Khusus Rahasia <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-premium" id="deskripsi_ciri_khusus" name="deskripsi_ciri_khusus" rows="3" required placeholder="Tanda tersembunyi seperti goresan, stiker, atau isi folder (Hanya diketahui pemilik asli)"></textarea>
                        <small class="text-primary extra-small fw-bold"><i class="bi bi-stars"></i> Memberikan informasi akurat sangat membantu verifikasi otomatis.</small>
                    </div>

                    <div class="mb-5 p-4 rounded-4 border-dashed border-2 text-start">
                        <label for="foto_bukti" class="form-label-premium"><i class="bi bi-camera-fill me-2"></i>Bukti Kepemilikan (Foto)</label>
                        <input class="form-control form-control-premium" type="file" id="foto_bukti" name="foto_bukti" accept="image/*">
                        <div class="p-3 bg-light rounded-3 mt-3 extra-small text-muted">Contoh: Kwitansi, Dus/Box, Foto Anda sedang menggunakan barang tersebut, atau STNK jika kendaraan.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-premium w-100 text-white shadow">
                        <i class="bi bi-shield-check me-2"></i> AJUKAN KLAIM SEKARANG
                    </button>
                </form>
            </div>
            <div class="p-3 bg-light text-center extra-small text-muted border-top fw-bold">
                Sistem Lost & Found PNP
            </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('id_kategori');
    const sections = document.querySelectorAll('.category-section');

    function updateFields() {
        const name = (categorySelect.options[categorySelect.selectedIndex]?.text || '').toLowerCase();
        sections.forEach(s => { s.classList.add('d-none'); s.querySelectorAll('input').forEach(i => i.disabled = true); });

        let active = document.getElementById('section-default');
        if (name.includes('surat') || name.includes('dokumen') || name.includes('kartu')) active = document.getElementById('section-surat');
        else if (name.includes('kendaraan') || name.includes('motor')) active = document.getElementById('section-kendaraan');
        else if (name.includes('elektronik') || name.includes('laptop') || name.includes('hp')) active = document.getElementById('section-elektronik');
        else if (name.includes('pakaian') || name.includes('baju') || name.includes('sepatu') || name.includes('tas')) active = document.getElementById('section-pakaian');

        active?.classList.remove('d-none');
        active?.querySelectorAll('input').forEach(i => i.disabled = false);
    }

    categorySelect.addEventListener('change', updateFields);
    if (categorySelect.value) updateFields();
});
</script>
@endpush
@endsection



