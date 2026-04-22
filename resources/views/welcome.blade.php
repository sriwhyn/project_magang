<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lost & Found PNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --pnp-navy: #001D39;
            --pnp-blue: #0A4174;
            --pnp-accent: #60A5FA;
            --pnp-gradient: linear-gradient(135deg, #001D39 0%, #0A4174 100%);
            --pnp-bg: #F8FAFC;
            --pnp-card-bg: #ffffff;
            --pnp-text: #001D39;
            --pnp-muted: #49769F;
            --pnp-border: #E2E8F0;
            --pnp-radius: 1rem;
        }
        body { font-family: 'Inter', sans-serif; color: var(--pnp-text); background: var(--pnp-bg); line-height: 1.5; }

        .navbar-top {
            background: var(--pnp-navy);
            backdrop-filter: blur(8px);
            padding: 0.8rem 0;
            position: sticky; top: 0; z-index: 1050;
            border-bottom: 2px solid rgba(255,255,255,0.05);
        }
        .navbar-top .nav-link {
            color: rgba(189, 216, 233, 0.7);
            font-size: 0.85rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
        }
        .navbar-top .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .btn-pnp-primary {
            background: var(--pnp-blue);
            border: none;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(10, 65, 116, 0.2);
            transition: all 0.3s ease;
            font-weight: 600;
            padding: 8px 20px;
        }
        .btn-pnp-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(10, 65, 116, 0.35);
            background: #08335b;
        }

        .hero-section {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #334155 100%);
            padding: 90px 0 110px;
        }
        .hero-section h1 {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 1.5rem;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .hero-section p {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
        }

        .feature-strip {
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .feature-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            height: 100%;
        }
        .feature-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
            transition: transform 0.3s;
        }
        .feature-card:hover .feature-icon-box {
            transform: scale(1.1);
        }

        .section-block {
            padding: 60px 0;
        }
        .section-block.alt-bg {
            background: #f8fafc;
        }
        .section-header {
            margin-bottom: 32px;
        }
        .section-header h3 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .section-header .section-line {
            display: block;
            width: 50px;
            height: 4px;
            border-radius: 10px;
            margin-top: 8px;
        }

        .item-card {
            background: #fff;
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .item-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            border-color: rgba(0,0,0,0.08);
        }
        .item-card .card-img {
            height: 190px;
            object-fit: cover;
            width: 100%;
        }
        .item-card .placeholder-img {
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .item-card .card-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .badge-soft {
            font-weight: 600;
            font-size: 0.72rem;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .badge-red { background: #fef2f2; color: #dc2626; }
        .badge-green { background: #ecfdf5; color: #059669; }
        .badge-yellow { background: #fffbeb; color: #d97706; }
        .badge-blue { background: #eef2ff; color: #0A4174; }
        .badge-gray { background: #f1f5f9; color: #49769F; }

        .meta-item {
            font-size: 0.8rem;
            color: #49769F;
            margin-bottom: 4px;
        }
        .meta-item i {
            margin-right: 6px;
            color: var(--pnp-blue);
        }

        .cta-box {
            background: linear-gradient(135deg, #001D39 0%, #0A4174 100%);
            border-radius: 16px;
            padding: 40px;
        }

        .footer-section {
            background: #111827;
            padding: 40px 0 20px;
        }
        .footer-section h6 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .footer-section a {
            color: #94a3b8;
            text-decoration: none;
        }
        .footer-section a:hover {
            color: #e2e8f0;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-top">
    <div class="container">
        <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2" href="/">
            <img src="{{ asset('assets/logo.png') }}" alt="PNP" height="32" class="rounded">
            <span>Lost & Found PNP</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('mahasiswa.kehilangan.index') }}"><i class="bi bi-search me-1"></i>Kehilangan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('mahasiswa.temuan.index') }}"><i class="bi bi-box-seam me-1"></i>Temuan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('mahasiswa.kerusakan.index') }}"><i class="bi bi-tools me-1"></i>Kerusakan</a></li>
            </ul>
            <ul class="navbar-nav align-items-center gap-2">
                @if(\Auth::check())
                    @if(\Auth::user()->role == 'admin')
                        <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="btn btn-pnp-primary btn-sm rounded-pill px-3">Dashboard Admin</a></li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf<button class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button></form>
                        </li>
                    @else
                        {{-- // mahasiswa & petugas --}}
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link text-white dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-bell me-1"></i>
                                @if(\Auth::user()->unreadNotifications->count() > 0)
                                <span id="notif-badge" class="badge bg-danger rounded-pill" style="font-size:0.65rem;">{{ \Auth::user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="width:300px;max-height:400px;overflow-y:auto;border-radius:12px;">
                                <li class="dropdown-header fw-bold">Notifikasi</li>
                                @forelse(\Auth::user()->unreadNotifications->take(10) as $notif)
                                    <li>
                                        <a class="dropdown-item small py-2 btn-read-notif" 
                                           href="#" 
                                           data-id="{{ $notif->id }}" 
                                           data-url="{{ $notif->data['url'] ?? '#' }}"
                                           style="cursor:pointer;">
                                            <strong>{{ $notif->data['judul'] ?? '' }}</strong><br>
                                            <span class="text-muted">{{ \Str::limit($notif->data['pesan'] ?? '', 50) }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="dropdown-item text-center text-muted small py-3">Tidak ada notifikasi</li>
                                @endforelse
                                @if(\Auth::user()->unreadNotifications->count() > 0)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-center small text-primary" href="{{ route('notifications.readAll') }}">Tandai semua dibaca</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            @php
                                $userName = \Auth::user()->role == 'petugas' ? (\Auth::user()->petugas->nama ?? \Auth::user()->email) : (\Auth::user()->mahasiswa->nama ?? \Auth::user()->email);
                                $profileRoute = \Auth::user()->role == 'petugas' ? route('petugas.dashboard') : route('mahasiswa.dashboard');
                            @endphp
                            <a class="nav-link text-white d-flex align-items-center gap-2 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width:30px;height:30px;font-size:0.75rem;font-weight:700;">{{ strtoupper(substr($userName, 0, 1)) }}</div>
                                <span class="d-none d-sm-inline small">{{ explode(' ', $userName)[0] }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius:12px;">
                                <li class="px-3 py-2 border-bottom">
                                    <strong class="d-block small">{{ $userName }}</strong>
                                    <small class="text-muted">{{ \Auth::user()->email }}</small>
                                    <div class="mt-1"><span class="badge bg-warning text-dark rounded-pill" style="font-size:0.7rem;"><i class="bi bi-star-fill me-1"></i>{{ \Auth::user()->poin ?? 0 }} Poin</span></div>
                                </li>
                                <li><a class="dropdown-item" href="{{ $profileRoute }}"><i class="bi bi-person me-2 text-primary"></i>Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li>
                            </ul>
                        </li>
                @endif
                @else
                    <li class="nav-item"><a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">Masuk</a></li>
                    <li class="nav-item"><a href="{{ route('register') }}" class="btn btn-pnp-primary btn-sm rounded-pill px-3">Daftar</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2 mb-3 fw-normal" style="font-size:0.82rem;">
                    <span class="bg-success rounded-circle d-inline-block me-1" style="width:7px;height:7px;"></span>
                    Platform Resmi Politeknik Negeri Padang
                </span>
                <h1 class="mb-3">Temukan Kembali<br><span style="color:#60a5fa;">Barang Berharga</span> Kamu</h1>
                <p class="opacity-75 mb-4" style="max-width:480px;font-size:0.95rem;line-height:1.7;">Lapor kehilangan, temukan barang, ajukan klaim, dan laporkan kerusakan fasilitas kampus politeknik negeri padang.</p>
                @if(\Auth::check())
                    @if(\Auth::user()->role == 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light fw-semibold rounded-pill px-4 py-2 me-2">Dashboard Admin</a>
                    @else
                        <a href="{{ route('mahasiswa.kehilangan.create') }}" class="btn btn-light fw-semibold rounded-pill px-4 py-2 me-2">Lapor Kehilangan</a>
                        <a href="{{ route('mahasiswa.temuan.create') }}" class="btn btn-outline-light rounded-pill px-4 py-2">Lapor Temuan</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-light fw-semibold rounded-pill px-4 py-2 me-2">Masuk Sekarang</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light rounded-pill px-4 py-2">Daftar Akun</a>
                @endif
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo PNP" height="140" class="rounded-3 shadow">
            </div>
        </div>
    </div>
</section>


@if(\Auth::check())
    @if(\Auth::user()->role=='mahasiswa' && isset($riwayatPelanggaran) && $riwayatPelanggaran->count()>0)
    <div class="container mt-4">
        <div class="alert alert-danger d-flex align-items-center rounded-3 border-0" style="background:#fef2f2;">
            <i class="bi bi-exclamation-triangle-fill text-danger me-2 fs-5"></i>
            <div><strong>Peringatan!</strong> {{ $riwayatPelanggaran->count() }} pelanggaran · {{ $riwayatPelanggaran->sum('poin') }} poin penalti. <a href="{{ route('mahasiswa.profile') }}" class="alert-link">Lihat Detail</a></div>
        </div>
    </div>
    @endif
@endif

<section id="kehilangan" class="section-block">
    {{-- // kehilangan --}}
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3><i class="bi bi-search text-primary me-2"></i>Laporan Kehilangan Terbaru</h3>
                <span class="section-line" style="background:#8EB1D1;"></span>
                <p class="text-muted mt-2 mb-0" style="font-size:0.9rem;">Barang yang dilaporkan hilang oleh civitas akademika PNP</p>
            </div>
            <a href="{{ \Auth::check() ? route('mahasiswa.kehilangan.index') : route('login') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2">Lihat Semua</a>
        </div>
        <div class="row g-3">
            @forelse($kehilangans ?? [] as $item)
                @php /** @var \App\Models\LaporanKehilangan $item */ @endphp
            <div class="col-md-6 col-lg-4">
                <div class="item-card">
                    @if(!empty($item->foto) && is_array($item->foto) && count($item->foto) > 0)
                        <img src="{{ asset('storage/' . $item->foto[0]) }}" alt="{{ $item->nama_barang }}" class="card-img" style="height: 190px; object-fit: cover;">
                    @else
                        <div class="placeholder-img" style="background:#eff6ff;">
                            <div class="text-center">
                                <i class="bi bi-shield-lock-fill text-primary" style="font-size:2.2rem;"></i>
                                <div class="text-primary fw-bold mt-1" style="font-size:0.75rem;">Tanpa Foto</div>
                            </div>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex gap-1 mb-2">
                            @if($item->status == 'selesai')
                                <span class="badge-soft badge-green">Ditemukan</span>
                            @else
                                <span class="badge-soft badge-red">Hilang</span>
                            @endif
                            <span class="badge-soft badge-blue">{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                        </div>
                        <h6 class="fw-bold mb-2" style="font-size:0.95rem;">{{ $item->nama_barang }}</h6>
                        <div class="mt-3">
                            @if(\Auth::check())
                                <a href="{{ route('mahasiswa.kehilangan.show', $item->id_laporan) }}" class="btn btn-pnp-primary w-100 btn-sm rounded-pill fw-semibold py-2">Lihat Detail</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-pnp-primary w-100 btn-sm rounded-pill py-2">Login untuk Info Lengkap</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="item-card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-inbox" style="font-size:2.5rem;color:#cbd5e1;"></i>
                        <h6 class="fw-bold mt-2 mb-1">Belum Ada Laporan</h6>
                        <p class="text-muted small mb-0">Saat ini tidak ada laporan barang hilang.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        @if(count($kehilangans ?? []) > 0)
        <div class="text-center mt-4">
            <a href="{{ \Auth::check() ? route('mahasiswa.kehilangan.index') : route('login') }}" class="btn btn-pnp-primary rounded-pill px-4">
                {{ \Auth::check() ? 'Lihat Semua Laporan Kehilangan' : 'Login untuk Lihat Selengkapnya' }}
            </a>
        </div>
        @endif
    </div>
</section>

<section id="temuan" class="section-block alt-bg">
    {{-- // temuan --}}
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3><i class="bi bi-box-seam text-primary me-2"></i>Barang Temuan Terbaru</h3>
                <span class="section-line" style="background:#0A4174;"></span>
                <p class="text-muted mt-2 mb-0" style="font-size:0.9rem;">Barang terverifikasi yang menunggu pemiliknya</p>
            </div>
            <a href="{{ \Auth::check() ? route('mahasiswa.temuan.index') : route('login') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2">Lihat Semua</a>
        </div>
        <div class="row g-3">
            @forelse($temuans ?? [] as $item)
                @php /** @var \App\Models\BarangTemuan $item */ @endphp
            <div class="col-md-6 col-lg-4">
                <div class="item-card">
                    <div class="placeholder-img" style="background: rgba(10, 65, 116, 0.05); border: 1px dashed rgba(10, 65, 116, 0.1);">
                        <div class="text-center">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size:2.2rem;"></i>
                            <div class="text-primary fw-bold mt-1" style="font-size:0.75rem;">Foto Dilindungi</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-1 mb-2">
                            @if($item->status_klaim == 'selesai')
                                <span class="badge-soft badge-blue">Sudah Diklaim</span>
                            @else
                                <span class="badge-soft badge-green">Tersedia di POB</span>
                            @endif
                            <span class="badge-soft badge-gray">{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                        </div>
                        <h6 class="fw-bold mb-2" style="font-size:0.95rem;">{{ $item->nama_barang }}</h6>
                        <p class="text-muted mb-2" style="font-size:0.82rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;line-clamp:2;overflow:hidden;"><i class="bi bi-shield-lock me-1"></i>Detail disembunyikan untuk keamanan.</p>
                        <div class="mb-3">
                            <div class="meta-item"><i class="bi bi-geo-alt"></i>{{ \Str::limit($item->lokasi_nama, 30) }}</div>
                            <div class="meta-item"><i class="bi bi-calendar3"></i>{{ \Carbon\Carbon::parse($item->tanggal_ditemukan)->format('d M Y') }}</div>
                        </div>
                        <div class="mt-auto">
                            @if(\Auth::check())
                                <a href="{{ route('mahasiswa.temuan.show', $item->id_barang) }}" class="btn btn-pnp-primary btn-sm w-100 rounded-pill fw-semibold py-2">Lihat Detail</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-pnp-primary btn-sm w-100 rounded-pill fw-semibold py-2">Login untuk Detai</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="item-card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-box2-heart" style="font-size:2.5rem;color:#cbd5e1;"></i>
                        <h6 class="fw-bold mt-2 mb-1">Belum Ada Temuan</h6>
                        <p class="text-muted small mb-0">Tidak ada barang temuan terverifikasi saat ini.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        @if(count($temuans ?? []) > 0)
        <div class="text-center mt-4">
            <a href="{{ \Auth::check() ? route('mahasiswa.temuan.index') : route('login') }}" class="btn btn-pnp-primary rounded-pill px-4">
                {{ \Auth::check() ? 'Lihat Semua Laporan Temuan' : 'Login untuk Lihat Selengkapnya' }}
            </a>
        </div>
        @endif
    </div>
</section>

<section id="kerusakan" class="section-block">
    {{-- // kerusakan --}}
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3><i class="bi bi-tools text-warning me-2"></i>Laporan Kerusakan Fasilitas</h3>
                <span class="section-line" style="background:#0A4174;"></span>
                <p class="text-muted mt-2 mb-0" style="font-size:0.9rem;">Status perbaikan fasilitas kampus yang dilaporkan</p>
            </div>
            <a href="{{ \Auth::check() ? route('mahasiswa.kerusakan.index') : route('login') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2">Lihat Semua</a>
        </div>
        <div class="row g-3">
            @forelse($kerusakans ?? [] as $item)
                @php /** @var \App\Models\LaporanKerusakan $item */ @endphp
            <div class="col-md-6 col-lg-4">
                <div class="item-card">
                    @if(!empty($item->gambar) && is_array($item->gambar) && count($item->gambar) > 0)
                        <img src="{{ asset('storage/' . $item->gambar[0]) }}" alt="{{ $item->judul_laporan }}" class="card-img">
                    @else
                        <div class="placeholder-img" style="background:#fffbeb;">
                            <div class="text-center">
                                <i class="bi bi-tools text-warning" style="font-size:2.2rem;"></i>
                                <div class="text-warning fw-bold mt-1" style="font-size:0.75rem;">Tanpa Foto</div>
                            </div>
                        </div>
                    @endif
                    <div class="card-body">
                        @php
                            $statusPerbaikan = $item->status_perbaikan ?? 'dilaporkan';
                            $sm = match($statusPerbaikan) {
                                'dilaporkan' => ['badge-red','Dilaporkan'],
                                'diproses' => ['badge-yellow','Diproses'],
                                'ditugaskan' => ['badge-blue','Ditugaskan'],
                                'dalam_pengerjaan' => ['badge-blue','Dikerjakan'],
                                'selesai' => ['badge-blue','Selesai'],
                                default => ['badge-gray', ucfirst($statusPerbaikan)],
                            };
                        @endphp
                        <div class="d-flex gap-1 mb-2">
                            <span class="badge-soft {{ $sm[0] }}">{{ $sm[1] }}</span>
                            <span class="badge-soft badge-gray">{{ $item->kategori->nama_kategori ?? 'Fasilitas' }}</span>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge-pnp {{ $sm[0] }}">{{ $sm[1] }}</span>
                            <span class="badge-pnp badge-category">{{ $item->kategori->nama_kategori ?? 'Fasilitas' }}</span>
                        </div>
                        <h5 class="fw-bold mb-2">{{ \Str::limit($item->judul_laporan, 40) }}</h5>
                        <div class="flex-grow-1">
                            <div class="meta-info"><i class="bi bi-geo-alt-fill"></i>{{ \Str::limit($item->lokasi_kerusakan, 35) }}</div>
                            <div class="meta-info"><i class="bi bi-clock-history"></i>{{ $item->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="mt-4">
                            @if(\Auth::check())
                                <a href="{{ route('mahasiswa.kerusakan.show', $item->id_laporan) }}" class="btn btn-pnp-primary w-100 btn-premium shadow-sm">Pantau Progress</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-pnp-primary w-100 btn-premium">Login untuk Pantau</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <h6 class="text-muted fw-bold">Tidak ada laporan kerusakan aktif.</h6>
            </div>
            @endforelse
        </div>
        
        @if(count($kerusakans ?? []) > 0)
        <div class="text-center mt-4">
            <a href="{{ \Auth::check() ? route('mahasiswa.kerusakan.index') : route('login') }}" class="btn btn-pnp-primary rounded-pill px-4">
                {{ \Auth::check() ? 'Lihat Semua Laporan Kerusakan' : 'Login untuk Lihat Selengkapnya' }}
            </a>
        </div>
        @endif
    </div>
</section>


<footer class="footer-section text-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('assets/logo.png') }}" height="32" class="rounded">
                    <span class="fw-bold">Lost & Found PNP</span>
                </div>
                <p class="small text-secondary mb-0" style="line-height:1.7;">Platform digital Politeknik Negeri Padang untuk pelaporan barang hilang, temuan, dan kerusakan fasilitas kampus.</p>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white">Navigasi</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('mahasiswa.kehilangan.index') }}">Kehilangan</a></li>
                    <li class="mb-2"><a href="{{ route('mahasiswa.temuan.index') }}">Temuan</a></li>
                    <li class="mb-2"><a href="{{ route('mahasiswa.kerusakan.index') }}">Kerusakan</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white">Kontak</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Limau Manis, Padang 25176</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i><a href="tel:081292389075" style="color:inherit;text-decoration:none;">081292389075</a></li>
                    <li class="mb-2"><a href="https://pnp.ac.id"><i class="bi bi-globe me-2"></i>www.pnp.ac.id</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white">Lokasi</h6>
                <a href="https://www.google.com/maps/place/Politeknik+Negeri+Padang/" target="_blank" class="btn btn-pnp-primary btn-sm rounded-pill w-100 mb-3">
                    <i class="bi bi-map me-1"></i>Buka di Google Maps
                </a>
                <div class="d-flex gap-2">
                    <a href="https://instagram.com/politekniknegeripadang_pnp" target="_blank" class="btn btn-pnp-primary btn-sm rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com/Pnp.ac.id" target="_blank" class="btn btn-pnp-primary btn-sm rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-facebook"></i></a>
                    <a href="https://youtube.com/@politeknikkampuspadang" target="_blank" class="btn btn-pnp-primary btn-sm rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4 mb-3">
        <p class="text-center text-secondary small mb-0">&copy; {{ date('Y') }} <strong class="text-light">Politeknik Negeri Padang</strong>. Seluruh Hak Cipta Dilindungi.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-read-notif').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');
            readNotif(id, url);
        });
    });
});

function readNotif(id, url) {
    fetch('/notifications/' + id + '/read', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(() => {
            const badge = document.getElementById('notif-badge');
            if (badge) {
                let count = parseInt(badge.textContent) - 1;
                if (count <= 0) badge.remove();
                else badge.textContent = count;
            }
            window.location.href = url;
        })
        .catch(() => { window.location.href = url; });
}
</script>
</body>
</html>


