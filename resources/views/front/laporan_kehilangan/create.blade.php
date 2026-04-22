@extends('layouts.front')
@section('title', 'Lapor Kehilangan Barang')

@push('styles')
<style>
    .premium-header { background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); color: white; padding: 2.5rem; border-radius: 12px 12px 0 0; }
    .premium-section-title { font-size: 0.75rem; font-weight: 800; color: #001D39; text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 6px; margin-bottom: 20px; margin-top: 25px; }
    .form-control-premium { border: 2px solid #cbd5e1; padding: 0.75rem 1rem; font-size: 0.85rem; border-radius: 10px; color: #001D39; font-weight: 500; }
    .form-control-premium:focus { border-color: #0A4174; box-shadow: 0 4px 12px rgba(0, 29, 57, 0.1); }
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
            <a href="{{ route('mahasiswa.kehilangan.index') }}" class="btn btn-link text-muted text-decoration-none p-0 extra-small fw-bold">
                <i class="bi bi-chevron-left"></i> KEMBALI KE DAFTAR
            </a>
        </div>

        {{-- MAIN FORM CARD --}}
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
            <div class="premium-header text-center">
                <h4 class="fw-800 mb-1" style="color: #facc15; text-shadow: 0 2px 4px rgba(0,0,0,0.2); letter-spacing: 1px;">LAPOR KEHILANGAN</h4>
                <p class="mb-0 extra-small opacity-75 fw-medium">Bantu kami mencatat data barang Anda agar mudah ditemukan.</p>
            </div>

            <div class="card-body p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger extra-small rounded-3 mb-4">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('mahasiswa.kehilangan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="premium-section-title">1. Apa yang Hilang?</div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label-premium">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-premium" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Dompet Hitam, Kunci KTM">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label-premium">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select form-control-premium" id="id_kategori" name="id_kategori" required>
                                <option value="">-- Pilih --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-1">
                        <div class="col-md-6"><label class="form-label-premium">Warna Dominan <span class="text-danger">*</span></label><input type="text" class="form-control form-control-premium" name="warna" value="{{ old('warna') }}" required placeholder="Hitam/Biru"></div>
                        <div class="col-md-6"><label class="form-label-premium">Tanggal Hilang <span class="text-danger">*</span></label><input type="date" class="form-control form-control-premium" name="tanggal_hilang" value="{{ old('tanggal_hilang', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required></div>
                    </div>

                    {{-- DYNAMIC FIELDS --}}
                    <div id="dynamic-fields" class="mt-3">
                        {{-- KENDARAAN --}}
                        <div class="category-section d-none" id="section-kendaraan">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label-premium">Merk</label><input type="text" class="form-control form-control-premium" name="merk" value="{{ old('merk') }}"></div>
                                <div class="col-md-4"><label class="form-label-premium">Tipe</label><input type="text" class="form-control form-control-premium" name="tipe_model" value="{{ old('tipe_model') }}"></div>
                                <div class="col-md-4"><label class="form-label-premium">No. Plat</label><input type="text" class="form-control form-control-premium" name="nomor_seri" value="{{ old('nomor_seri') }}"></div>
                            </div>
                        </div>
                        {{-- SURAT --}}
                        <div class="category-section d-none" id="section-surat">
                            <div class="row g-2">
                                <div class="col-md-6"><label class="form-label-premium">Jenis Surat</label><input type="text" class="form-control form-control-premium" name="tipe_model"></div>
                                <div class="col-md-6"><label class="form-label-premium">No. Identitas</label><input type="text" class="form-control form-control-premium" name="nomor_seri"></div>
                            </div>
                        </div>
                        {{-- ELEKTRONIK --}}
                        <div class="category-section d-none" id="section-elektronik">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label-premium">Merk</label><input type="text" class="form-control form-control-premium" name="merk"></div>
                                <div class="col-md-4"><label class="form-label-premium">Model</label><input type="text" class="form-control form-control-premium" name="tipe_model"></div>
                                <div class="col-md-4"><label class="form-label-premium">IMEI/SN</label><input type="text" class="form-control form-control-premium" name="nomor_seri"></div>
                            </div>
                        </div>

                    </div>

                    <div class="premium-section-title">2. Detail Lokasi & Kejadian</div>
                    <div class="mb-3">
                        <label class="form-label-premium">Perkiraan Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-premium" name="lokasi_hilang" value="{{ old('lokasi_hilang') }}" required placeholder="Contoh: Gedung F2, Musholla PKM">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium">Ciri Khusus / Tanda Unik <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-premium" name="ciri_khusus" rows="2" required placeholder="Sebutkan tanda khusus: stiker, goresan, gantungan pin, dsb...">{{ old('ciri_khusus') }}</textarea>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label-premium">Deskripsi Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-premium" name="deskripsi" rows="4" required placeholder="Jelaskan detail kronologi kehilangan anda...">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="mb-5 text-start">
                        <label class="form-label-premium">Foto Barang (Jika ada)</label>
                        <input type="file" class="form-control form-control-premium" name="foto[]" multiple accept="image/*">
                        <small class="text-muted extra-small d-block mt-1">Gunakan foto asli jika ada untuk mempermudah pencarian.</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-premium w-100 text-white shadow">
                        <i class="bi bi-megaphone me-2"></i> PUBLIKASIKAN LAPORAN KEHILANGAN
                    </button>
                    
                    <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4">
                        <p class="mb-0 extra-small text-muted" style="line-height: 1.5;">
                            <strong>Tips:</strong> Pastikan data spesifikasi barang Anda benar. Petugas akan menghubungi Anda melalui sistem ini jika terdapat info penemuan.
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('id_kategori');
    const sections = document.querySelectorAll('.category-section');
    function updateFields() {
        const name = (categorySelect.options[categorySelect.selectedIndex]?.text || '').toLowerCase();
        sections.forEach(s => { s.classList.add('d-none'); s.querySelectorAll('input').forEach(i => i.disabled = true); });
        let active = document.getElementById('section-default');
        if (name.includes('kendaraan') || name.includes('motor') || name.includes('mobil')) active = document.getElementById('section-kendaraan');
        else if (name.includes('surat') || name.includes('dokumen') || name.includes('kartu')) active = document.getElementById('section-surat');
        else if (name.includes('elektronik') || name.includes('laptop') || name.includes('hp') || name.includes('gadget')) active = document.getElementById('section-elektronik');
        active?.classList.remove('d-none');
        active?.querySelectorAll('input').forEach(i => i.disabled = false);
    }
    categorySelect.addEventListener('change', updateFields);
    if (categorySelect.value) updateFields();
});
</script>
@endpush
