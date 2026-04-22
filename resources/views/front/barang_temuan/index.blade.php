@extends('layouts.front')
@section('title', 'Daftar Barang Temuan')

@push('styles')
<style>
    .index-header { background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); border-radius: 24px; padding: 3rem 2rem; color: white; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(0,29,57,0.15); }
    .index-header h1 { color: #ffffff !important; font-weight: 900; }
    .index-header p { color: rgba(255,255,255,0.8) !important; font-weight: 500; }
    .premium-card { border: none; border-radius: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: white; border: 1px solid #f1f5f9; overflow: hidden; height: 100%; position: relative; }
    .premium-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #0d6efd44; }
    .card-img-container { height: 180px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
    .card-img-container i { font-size: 3rem; color: #cbd5e1; transition: all 0.3s; }
    .premium-card:hover .card-img-container i { transform: scale(1.1); color: #0d6efd; }
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
    {{-- // header --}}
    <div class="index-header text-center animate-fade">
        <h6 class="fw-800 text-uppercase mb-2" style="letter-spacing: 3px; color: #facc15;">Temuan Kampus</h6>
        <h1 class="fw-900 mb-3 display-5" style="color: #ffffff !important;">BARANG TEMUAN</h1>
        <p class="fw-500 mb-4 mx-auto" style="max-width: 600px; color: rgba(255,255,255,0.8) !important;">Wadah aman bagi civitas akademika untuk mengembalikan barang yang ditemukan di lingkungan kampus PNP.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('mahasiswa.temuan.create') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-800 shadow-sm border-0">
                <i class="bi bi-plus-lg me-1"></i> LAPOR TEMUAN
            </a>
        </div>
    </div>

    {{-- // Filter & Search --}}
    <div class="row justify-content-center mb-5 animate-fade">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <div class="card-body p-3">
                    <form action="{{ route('mahasiswa.temuan.index') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-primary"></i></span>
                                <input type="text" name="q" class="form-control border-0 bg-white" placeholder="Cari barang (cth: kunci, tas, samsung)..." value="{{ request('q') }}" style="border-radius: 0 12px 12px 0; box-shadow: none;">
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
                    <a href="{{ route('mahasiswa.temuan.index') }}" class="text-decoration-none small fw-bold text-muted animate-fade">
                        <i class="bi bi-x-circle me-1"></i> Reset Pencarian
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- // pribadi --}}
    @if(\Auth::check() && isset($myItems) && $myItems->count() > 0)
    <div class="mb-5">
        <h5 class="section-title"><i class="bi bi-person-circle text-primary"></i> Laporan Saya</h5>
        <div class="row g-4">
            @foreach($myItems as $item)
            <div class="col-md-6 col-lg-4 animate-fade">
                <div class="premium-card">
                    @php
                        $statusText = match($item->status_penyerahan) {
                            'sudah_diterima' => 'DI ADMIN',
                            default => 'PERLU DISERAHKAN'
                        };
                        $statusClass = match($item->status_penyerahan) {
                            'sudah_diterima' => 'bg-success text-white',
                            default => 'bg-warning text-dark'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    <div class="p-4">
                        <span class="category-label">{{ $item->kategori->nama_kategori }}</span>
                        <h3 class="item-title">{{ $item->nama_barang }}</h3>
                        <div class="item-meta mb-4">
                            <span><i class="bi bi-calendar me-1"></i> {{ $item->created_at->format('d M Y') }}</span>
                            <span><i class="bi bi-geo-alt me-1"></i> {{ Str::limit($item->lokasi_nama, 15) }}</span>
                        </div>
                        <a href="{{ route('mahasiswa.temuan.show', $item->id_barang) }}" class="btn btn-primary rounded-pill w-100 fw-800 extra-small py-2">MANAJEMEN LAPORAN</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- // list --}}
    <div class="mb-5">
        <h5 class="section-title"><i class="bi bi-grid-fill text-primary"></i> Galeri Temuan Terverifikasi</h5>
        <div class="row g-4">
            @forelse($barang as $item)
            @php $isDiklaim = $item->status_klaim == 'selesai'; @endphp
            <div class="col-md-6 col-lg-4 animate-fade">
                <div class="premium-card {{ $isDiklaim ? 'opacity-50' : '' }}">
                    @if($isDiklaim)
                        <span class="status-badge bg-secondary text-white"><i class="bi bi-check-circle-fill me-1"></i> SUDAH DIAMBIL</span>
                    @elseif($item->status_klaim == 'proses')
                        <span class="status-badge bg-info text-white">DALAM PROSES KLAIM</span>
                    @else
                        <span class="status-badge bg-success text-white">TERSEDIA</span>
                    @endif

                    <div class="card-img-container">
                        <i class="bi bi-shield-lock"></i>
                        <div class="position-absolute bottom-0 w-100 p-2 text-center" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(4px);">
                            <span class="extra-small fw-800 text-muted">FOTO DILINDUNGI</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <span class="category-label">{{ $item->kategori->nama_kategori }}</span>
                        <h3 class="item-title">{{ $item->nama_barang }}</h3>
                        <div class="item-meta mb-3">
                            <span><i class="bi bi-calendar me-1"></i> {{ $item->tanggal_ditemukan->format('d M Y') }}</span>
                            <span><i class="bi bi-geo-alt me-1"></i> {{ Str::limit($item->lokasi_nama, 15) }}</span>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('mahasiswa.temuan.show', $item->id_barang) }}" class="btn btn-outline-primary rounded-pill px-4 fw-800 extra-small flex-grow-1">DETAIL</a>
                            @if(!$isDiklaim && $item->id_user_pelapor != \Auth::id() && $item->status_penyerahan == 'sudah_diterima')
                            <a href="{{ route('mahasiswa.klaim.create', ['id_barang' => $item->id_barang]) }}" class="btn btn-primary rounded-pill px-4 fw-800 extra-small">KLAIM</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-inbox text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold text-muted">Belum ada barang temuan baru.</h5>
                <p class="text-muted small">Cek kembali nanti atau laporkan jika Anda menemukan barang.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- // halaman --}}
    <div class="mt-5 d-flex justify-content-center">
        @auth
            {{ $barang->appends(request()->query())->links() }}
        @else
            <div class="text-center">
                <p class="text-muted small fw-bold mb-3">Teks lengkap dan detail lainnya memerlukan login.</p>
                <a href="{{ route('login') }}" class="btn btn-dark rounded-pill px-5 fw-800">LOGIN KE SISTEM</a>
            </div>
        @endauth
    </div>
</div>
@endsection



