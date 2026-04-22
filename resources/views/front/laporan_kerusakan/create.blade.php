@extends('layouts.front')
@section('title', 'Lapor Kerusakan Fasilitas')

@push('styles')
<style>
    .premium-card { border: none; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.08); overflow: hidden; background: white; }
    .premium-header { 
        background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); 
        color: white; padding: 1.5rem; text-align: left; 
        display: flex; align-items: center; justify-content: space-between;
    }
    .form-label-premium { font-size: 0.8rem; font-weight: 800; color: #001D39; text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 0.5px; }
    .form-control-premium { 
        border: 2px solid #cbd5e1; border-radius: 12px; padding: 0.75rem 1rem; 
        font-size: 0.9rem; font-weight: 600; color: #001D39; transition: all 0.2s;
        background: #ffffff;
    }
    .form-control-premium:focus { 
        border-color: #0A4174; box-shadow: 0 4px 12px rgba(0, 29, 57, 0.1); 
        background: white; outline: none;
    }
    .btn-premium { 
        border-radius: 50px; padding: 1rem 2rem; font-weight: 800; 
        text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s;
        border: none; font-size: 0.85rem;
    }
    .btn-navy { background: #001D39; color: white; }
    .btn-navy:hover { background: #0A4174; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 29, 57, 0.2); }
    
    .section-divider { position: relative; margin: 2rem 0 1.5rem; border-top: 2px dashed #e2e8f0; }
    .section-badge { 
        position: absolute; top: -12px; left: 0; background: #f1f5f9; 
        color: #001D39; font-size: 0.65rem; font-weight: 900; 
        padding: 2px 12px; border-radius: 50px; text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- // header --}}
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('mahasiswa.kerusakan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Batal
            </a>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-800" style="font-size: 0.7rem;">LAPORAN KERUSAKAN BARU</span>
        </div>

        <div class="premium-card">
            <div class="premium-header">
                <div>
                    <h2 class="fw-900 mb-0 text-white" style="letter-spacing: -0.5px;">LAPOR KERUSAKAN</h2>
                    <p class="opacity-75 extra-small mb-0 fw-bold text-white-50 text-uppercase">Bantu kami menjaga kualitas fasilitas kampus</p>
                </div>
                <i class="bi bi-tools fs-2 opacity-30 text-white"></i>
            </div>

            <div class="p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 mb-4 small fw-bold">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                {{-- // form --}}
                <form action="{{ route('mahasiswa.kerusakan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="section-divider">
                        <span class="section-badge">Identitas Masalah</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label-premium">Judul Laporan / Objek Rusak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-premium" name="judul_laporan" value="{{ old('judul_laporan') }}" required placeholder="Contoh: Lampu Ruang Kelas A101 Mati">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-premium">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select form-control-premium" name="id_kategori" required>
                                <option value="">-- Pilih --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span class="section-badge">Lokasi & Detail</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-premium">Lokasi Spesifik Kejadian <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-2 border-end-0" style="border-color: #e2e8f0; border-radius: 12px 0 0 12px;"><i class="bi bi-geo-alt-fill text-primary"></i></span>
                            <input type="text" class="form-control form-control-premium border-start-0" name="lokasi_kerusakan" value="{{ old('lokasi_kerusakan') }}" required style="border-radius: 0 12px 12px 0;" placeholder="Gedung, Lantai, Nama Ruangan...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-premium" name="deskripsi" rows="4" required placeholder="Ceritakan detail kerusakan yang Anda temukan agar teknisi kami dapat mempersiapkan peralatan yang tepat...">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label-premium">Lampiran Foto Bukti (Opsional)</label>
                        <div class="p-4 border-2 border-dashed rounded-4 text-center bg-light" style="border-color: #e2e8f0;">
                            <input type="file" class="form-control form-control-sm border-0 bg-transparent shadow-none w-auto d-inline-block" name="gambar[]" multiple accept="image/*">
                            <p class="text-muted extra-small fw-bold mb-0 mt-2 text-uppercase">Maksimal 3 Foto &bull; Format: JPG, PNG, JPEG</p>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-navy btn-premium shadow-sm">
                            <i class="bi bi-send-check-fill me-2"></i> KIRIM LAPORAN KERUSAKAN
                        </button>
                    </div>

                    <div class="mt-4 p-3 rounded-4 border-start border-primary border-4 bg-primary bg-opacity-10">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            <span class="extra-small fw-900 text-primary text-uppercase">Pemberitahuan</span>
                        </div>
                        <p class="mb-0 extra-small text-muted fw-bold">
                            Laporan akan ditinjau dalam 1x24 jam. Anda akan menerima notifikasi setiap ada perubahan status perbaikan.
                        </p>
                    </div>
                </form>
            </div>
            
            <div class="card-footer bg-light p-4 text-center">
                <p class="extra-small text-muted fw-bold mb-0 text-uppercase letter-spacing-1">Sistem Lost & Found PNP &bull; Bersama Jaga Fasilitas Kampus</p>
            </div>
        </div>
    </div>
</div>
@endsection
