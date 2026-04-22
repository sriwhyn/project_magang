@extends('layouts.front')
@section('title', 'Daftar Laporan Kehilangan')

@push('styles')
<style>
    .index-header { background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); border-radius: 24px; padding: 3rem 2rem; color: white; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(0,29,57,0.15); }
    .index-header h1 { color: #ffffff !important; font-weight: 900; }
    .index-header p { color: rgba(255,255,255,0.8) !important; font-weight: 500; }
    .premium-card { border: none; border-radius: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: white; border: 1px solid #f1f5f9; overflow: hidden; height: 100%; position: relative; }
    .premium-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #0d6efd44; }
    .card-img-container { height: 180px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
    .card-img-container img { width: 100%; height: 100%; object-fit: cover; }
    .card-img-container i { font-size: 3rem; color: #cbd5e1; transition: all 0.3s; }
    .status-badge { position: absolute; top: 12px; right: 12px; z-index: 5; padding: 6px 12px; border-radius: 50px; font-weight: 800; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .category-label { font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block; }
    .item-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; line-height: 1.3; }
    .item-meta { font-size: 0.75rem; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 12px; }
    .item-meta i { color: #94a3b8; }
    .section-title { font-size: 0.85rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
    .section-title::after { content: ''; height: 2px; background: #e2e8f0; flex-grow: 1; }
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Main Header --}}
    <div class="index-header text-center animate-fade">
        <h6 class="fw-800 text-uppercase mb-2" style="letter-spacing: 3px; color: #facc15;">Bantuan Pencarian</h6>
        <h1 class="fw-900 mb-3 display-5 text-uppercase" style="color: #ffffff !important;">Laporan Kehilangan</h1>
        <p class="fw-500 mb-4 mx-auto" style="max-width: 600px; color: rgba(255,255,255,0.8) !important;">Laporkan barang yang hilang agar seluruh komunitas kampus dapat membantu Anda menemukannya kembali.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('mahasiswa.kehilangan.create') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-800 shadow-sm border-0">
                <i class="bi bi-plus-lg me-1"></i> LAPOR KEHILANGAN
            </a>
        </div>
    </div>

    {{-- // Filter & Search --}}
    <div class="row justify-content-center mb-5 animate-fade">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <div class="card-body p-3">
                    <form action="{{ route('mahasiswa.kehilangan.index') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-primary"></i></span>
                                <input type="text" name="q" class="form-control border-0 bg-white" placeholder="Cari barang hilang (cth: tas, dompet)..." value="{{ request('q') }}" style="border-radius: 0 12px 12px 0; box-shadow: none;">
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
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-800 shadow-sm">
                                <i class="bi bi-sliders me-1"></i> FILTER DATA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @if(request()->filled('q') || request()->filled('category'))
                <div class="mt-3 text-center">
                    <a href="{{ route('mahasiswa.kehilangan.index') }}" class="text-decoration-none small fw-bold text-muted animate-fade">
                        <i class="bi bi-x-circle me-1"></i> Reset Pencarian
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Public Listing --}}
    <div class="mb-5">
        <h5 class="section-title"><i class="bi bi-search text-primary"></i> Daftar Kehilangan Publik</h5>
        <div class="row g-4">
            @forelse($allItems as $item)
            <div class="col-md-6 col-lg-4 animate-fade">
                <div class="premium-card">
                    @php 
                        $statusColor = match($item->status) { 
                            'menunggu' => 'bg-warning text-dark', 
                            'diproses' => 'bg-info text-white', 
                            'selesai' => 'bg-success text-white', 
                            'ditolak' => 'bg-danger text-white', 
                            default => 'bg-secondary text-white' 
                        };
                    @endphp
                    <span class="status-badge {{ $statusColor }}">{{ $item->status == 'selesai' ? 'DITEMUKAN' : strtoupper($item->status) }}</span>

                    <div class="card-img-container">
                        @if(!empty($item->foto) && is_array($item->foto) && count($item->foto) > 0)
                            <img src="{{ asset('storage/' . $item->foto[0]) }}" alt="{{ $item->nama_barang }}">
                        @else
                            <i class="bi bi-camera-off"></i>
                            <div class="position-absolute bottom-0 w-100 p-2 text-center" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(4px);">
                                <span class="extra-small fw-800 text-muted">TANPA FOTO</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <span class="category-label">{{ $item->kategori->nama_kategori }}</span>
                        <h3 class="item-title text-uppercase">{{ $item->nama_barang }}</h3>
                        <div class="item-meta mb-3">
                            <span><i class="bi bi-calendar me-1"></i> {{ $item->tanggal_hilang->format('d M Y') }}</span>
                            <span><i class="bi bi-geo-alt me-1"></i> {{ Str::limit($item->lokasi_hilang, 15) }}</span>
                        </div>
                        <p class="small text-muted mb-4 fw-500" style="height: 3rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $item->deskripsi }}</p>
                        <a href="{{ route('mahasiswa.kehilangan.show', $item->id_laporan) }}" class="btn btn-primary rounded-pill w-100 fw-800 extra-small py-2 mt-auto">LIHAT DETAIL</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-search text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold text-muted">Belum ada laporan kehilangan publik.</h5>
                <p class="text-muted small">Syukurlah, sepertinya semua barang kini sudah aman.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-5 d-flex justify-content-center">
        @auth
            {{ $allItems->appends(request()->query())->links() }}
        @else
            <div class="text-center">
                <p class="text-muted small fw-bold mb-3">Detail lengkap pelaporan memerlukan login.</p>
                <a href="{{ route('login') }}" class="btn btn-dark rounded-pill px-5 fw-800">LOGIN KE SISTEM</a>
            </div>
        @endauth
    </div>
</div>
@endsection




