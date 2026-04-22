@extends('layouts.front')
@section('title', 'Laporan Kerusakan Fasilitas')

@push('styles')
<style>
    .index-header { background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); border-radius: 24px; padding: 3.5rem 2rem; color: white; margin-bottom: 3rem; box-shadow: 0 15px 35px rgba(0,29,57,0.15); }
    .index-header h1 { color: #ffffff !important; font-weight: 900; }
    .index-header p { color: rgba(255,255,255,0.8) !important; font-weight: 500; }
    .premium-card { border: none; border-radius: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: white; border: 1px solid #f1f5f9; overflow: hidden; height: 100%; position: relative; }
    .premium-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #0A417444; }
    .card-img-container { height: 180px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
    .card-img-container img { width: 100%; height: 100%; object-fit: cover; }
    .card-img-container i { font-size: 3rem; color: #cbd5e1; transition: all 0.3s; }
    
    .status-badge { position: absolute; top: 12px; right: 12px; z-index: 5; padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .category-label { font-size: 0.65rem; font-weight: 800; color: #0A4174; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block; }
    .item-title { font-size: 1rem; font-weight: 800; color: #001D39; margin-bottom: 8px; line-height: 1.3; }
    .item-meta { font-size: 0.75rem; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .item-meta i { color: #0A4174; }
    
    .section-title { font-size: 0.8rem; font-weight: 900; color: #001D39; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; }
    .section-title::after { content: ''; height: 2px; background: #f1f5f9; flex-grow: 1; }
    
    .btn-lapor { 
        background: #facc15; color: #001D39; border: none; font-weight: 800; 
        padding: 0.8rem 2rem; border-radius: 50px; transition: all 0.3s;
        text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;
    }
    .btn-lapor:hover { background: #eab308; transform: scale(1.05); box-shadow: 0 10px 20px rgba(234, 179, 8, 0.3); color: #001D39; }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Main Header --}}
    <div class="index-header text-center">
        <h6 class="fw-800 text-uppercase mb-2" style="letter-spacing: 3px; color: #ffeb3b; font-size: 0.75rem;">Maintenance Kampus</h6>
        <h1 class="fw-900 mb-3 display-5 text-uppercase" style="letter-spacing: -1px; color: #ffffff !important;">Laporan Kerusakan</h1>
        <p class="fw-500 mb-4 mx-auto" style="max-width: 600px; font-size: 0.95rem; color: rgba(255,255,255,0.8) !important;">Bantu kami menjaga fasilitas kampus dengan melaporkan setiap kerusakan yang Anda temui di area Politeknik Negeri Padang.</p>
        <div class="d-flex justify-content-center pt-2">
            <a href="{{ route('mahasiswa.kerusakan.create') }}" class="btn btn-lapor">
                <i class="bi bi-tools me-2"></i> LAPOR KERUSAKAN
            </a>
        </div>
    </div>

    {{-- // Filter & Search --}}
    <div class="row justify-content-center mb-5 animate-fade">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <div class="card-body p-3">
                    <form action="{{ route('mahasiswa.kerusakan.index') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-primary"></i></span>
                                <input type="text" name="q" class="form-control border-0 bg-white" placeholder="Cari fasilitas atau lokasi..." value="{{ request('q') }}" style="border-radius: 0 12px 12px 0; box-shadow: none;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select border-0 bg-white" style="border-radius: 12px; box-shadow: none;">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $cat)
                                    <option value="{{ $cat->id_kategori }}" {{ request('category') == $cat->id_kategori ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-800 shadow-sm" style="background: #001D39; border: none; padding: 0.7rem;">
                                <i class="bi bi-sliders me-1"></i> FILTER DATA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @if(request()->filled('q') || request()->filled('category'))
                <div class="mt-3 text-center">
                    <a href="{{ route('mahasiswa.kerusakan.index') }}" class="text-decoration-none small fw-bold text-muted animate-fade">
                        <i class="bi bi-x-circle me-1"></i> Reset Pencarian
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Public Listing --}}
    <div class="mb-5">
        <h5 class="section-title"><i class="bi bi-shield-check text-primary"></i> Status Perbaikan Fasilitas</h5>
        <div class="row g-4">
            @forelse($allItems as $item)
            <div class="col-md-6 col-lg-4">
                <div class="premium-card">
                    @php
                        $statusColor = match($item->status_perbaikan) { 
                            'dilaporkan' => 'bg-secondary text-white', 
                            'menunggu_petugas' => 'bg-warning text-dark', 
                            'diproses' => 'bg-info text-white', 
                            'dalam_pengerjaan' => 'bg-primary text-white', 
                            'selesai' => 'bg-success text-white', 
                            default => 'bg-secondary text-white' 
                        };
                    @endphp
                    <span class="status-badge {{ $statusColor }}">{{ str_replace('_', ' ', strtoupper($item->status_perbaikan)) }}</span>

                    <div class="card-img-container">
                        @php $fotos = is_array($item->gambar) ? $item->gambar : json_decode($item->gambar, true); @endphp
                        @if(!empty($fotos) && count($fotos) > 0)
                            <img src="{{ asset('storage/' . $fotos[0]) }}" alt="{{ $item->judul_laporan }}">
                        @else
                            <i class="bi bi-tools opacity-25"></i>
                            <div class="position-absolute bottom-0 w-100 p-2 text-center" style="background: rgba(0,0,0,0.03);">
                                <span class="extra-small fw-800 text-muted opacity-50">TANPA FOTO</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <span class="category-label">{{ $item->kategori->nama_kategori }}</span>
                        <h3 class="item-title text-uppercase">{{ $item->judul_laporan }}</h3>
                        
                        <div class="d-flex flex-column gap-2 mt-3 mb-4">
                            <div class="item-meta">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ Str::limit($item->lokasi_kerusakan, 30) }}</span>
                            </div>
                            <div class="item-meta extra-small opacity-75">
                                <i class="bi bi-person-circle"></i>
                                <span>Pelapor: {{ $item->pelapor->mahasiswa?->nama ?? 'Civitas Kampus' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('mahasiswa.kerusakan.show', $item->id_laporan) }}" class="btn btn-navy w-100 rounded-pill fw-800 extra-small py-2 mt-auto" style="background: #001D39; color: white;">
                            LIHAT DETAIL PROGRES
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 100px; height: 100px; background: #f8fafc !important;">
                    <i class="bi bi-patch-check-fill text-success" style="font-size: 3.5rem;"></i>
                </div>
                <h4 class="fw-900 text-dark mb-1">SEMUA FASILITAS BAIK</h4>
                <p class="text-muted small fw-bold">Belum ada laporan kerusakan yang masuk saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-5 d-flex justify-content-center">
        @auth
            {{ $allItems->appends(request()->query())->links() }}
        @else
            <div class="text-center p-5 bg-light rounded-4 border border-dashed">
                <i class="bi bi-lock-fill text-muted mb-3 d-block" style="font-size: 2rem;"></i>
                <p class="text-muted small fw-bold mb-3">Login untuk melihat detail laporan dan riwayat perbaikan lengkap.</p>
                <a href="{{ route('login') }}" class="btn btn-lapor px-5">LOGIN KE SISTEM</a>
            </div>
        @endauth
    </div>
</div>
@endsection
