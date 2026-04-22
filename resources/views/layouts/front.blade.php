<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lost and Found PNP')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --pnp-navy: #001D39;
            --pnp-blue: #0A4174;
            --pnp-accent: #7BBDE8;
            --pnp-gradient: linear-gradient(135deg, #001D39 0%, #0A4174 100%);
            --pnp-bg: #E3EDF5;
            --pnp-card-bg: #ffffff;
            --pnp-text: #001D39;
            --pnp-muted: #49769F;
            --pnp-border: #91b5ce;
            --pnp-radius: 1rem;
            --pnp-radius-sm: 0.75rem;
        }
        ::placeholder { color: #4b5563 !important; opacity: 1; font-weight: 500; }
        .form-text { color: #2c3e50 !important; font-weight: 600; font-size: 0.75rem; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--pnp-bg); color: var(--pnp-text); margin: 0; min-height: 100vh; display: flex; flex-direction: column; line-height: 1.5; }

        .sc-navbar {
            background: rgba(0, 29, 57, 0.98);
            backdrop-filter: blur(12px);
            padding: 0.5rem 0;
            box-shadow: 0 4px 20px rgba(0, 29, 57, 0.15);
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .sc-navbar .navbar-brand { font-weight: 800; letter-spacing: -0.5px; font-size: 1.1rem; color: #fff !important; }
        .sc-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 14px !important;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .sc-navbar .nav-link:hover,
        .sc-navbar .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.1); }

        .sc-card {
            background: var(--pnp-card-bg);
            border: 1px solid var(--pnp-border);
            border-radius: var(--pnp-radius);
            box-shadow: 0 4px 25px -5px rgba(0, 29, 57, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        .sc-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px -10px rgba(0, 29, 57, 0.15); }

        .btn-pnp-primary {
            background: var(--pnp-blue);
            border: none;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(10, 65, 116, 0.25);
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 10px 24px;
        }
        .btn-pnp-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(10, 65, 116, 0.35); background: #08335b; }

        h1, h2, h3, h4 { font-weight: 800; letter-spacing: -0.02em; }
        
        /* Heading Colors */
        body:not(.dark-mode) h1, body:not(.dark-mode) h2, body:not(.dark-mode) h3, body:not(.dark-mode) h4 { color: var(--pnp-navy); }
        
        /* Dark Container Overrides (Ultra Strong Specifity) */
        .index-header, .premium-header {
            --bs-heading-color: #ffffff !important;
            color: #ffffff !important;
        }
        .index-header h1, .index-header h2, .index-header h3, .index-header p,
        .premium-header h1, .premium-header h2, .premium-header h3, .premium-header p {
            color: #ffffff !important;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
        }
        
        body .text-white h1, body .text-white h2, body .text-white h3 { color: #ffffff !important; }
        h1 { font-size: 2.25rem; }
        h2 { font-size: 1.75rem; }
        h3 { font-size: 1.35rem; }

        .text-primary { color: var(--pnp-blue) !important; }
        .bg-primary { background-color: var(--pnp-blue) !important; }
        .btn-primary { background-color: var(--pnp-blue) !important; border-color: var(--pnp-blue) !important; color: #fff !important; }
        .btn-primary:hover { background-color: #08335b !important; border-color: #08335b !important; }
        .btn-outline-primary { color: var(--pnp-blue) !important; border-color: var(--pnp-blue) !important; font-weight: 600; }
        .btn-outline-primary:hover { background-color: var(--pnp-blue) !important; color: #fff !important; }
        .badge.bg-primary { background-color: var(--pnp-blue) !important; color: #fff !important; padding: 6px 12px; font-weight: 700; }

        .sc-section-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--pnp-navy);
            border-left: 4px solid var(--pnp-blue);
            padding-left: 14px;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--pnp-navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: #fff;
            border: 2px solid var(--pnp-border);
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 0.9rem;
            color: var(--pnp-navy);
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--pnp-blue);
            box-shadow: 0 0 0 4px rgba(10, 65, 116, 0.1);
        }

        .img-lightbox { cursor: zoom-in; transition: transform 0.2s; }
        .img-lightbox:hover { transform: scale(1.02); }

        /* Global Progress Stepper */
        .sc-stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
            padding: 0;
            width: 100%;
        }
        .sc-stepper::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #cbd5e1;
            z-index: 1;
        }
        .sc-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .sc-step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b;
            transition: all 0.3s;
            margin-bottom: 8px;
            box-shadow: 0 0 0 4px #fff;
        }
        .sc-step.active .sc-step-icon {
            border-color: var(--pnp-blue);
            background: var(--pnp-blue);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(10, 65, 116, 0.1), 0 0 0 4px #fff;
        }
        .sc-step.completed .sc-step-icon {
            border-color: #22c55e;
            background: #22c55e;
            color: #fff;
            box-shadow: 0 0 0 4px #fff;
        }
        .sc-step-label {
            font-size: 0.6rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.5px;
            max-width: 100%;
            word-wrap: break-word;
        }
        .sc-step.active .sc-step-label { color: var(--pnp-navy); }
        .sc-step.completed .sc-step-label { color: #16a34a; }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg sc-navbar">
    <div class="container">
        <a class="navbar-brand text-white d-flex align-items-center gap-2" href="/">
            <div class="bg-white rounded-3 p-1 d-flex shadow-sm"><img src="{{ asset('assets/logo.png') }}" height="28"></div>
            Lost & Found PNP
        </a>
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMhs">
            <i class="bi bi-list fs-1"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMhs">
            <ul class="navbar-nav me-auto ps-lg-4">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('mahasiswa.kehilangan.*') ? 'active' : '' }}" href="{{ route('mahasiswa.kehilangan.index') }}"><i class="bi bi-search me-1"></i>Kehilangan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('mahasiswa.temuan.*') ? 'active' : '' }}" href="{{ route('mahasiswa.temuan.index') }}"><i class="bi bi-box-seam me-1"></i>Temuan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('mahasiswa.kerusakan.*') ? 'active' : '' }}" href="{{ route('mahasiswa.kerusakan.index') }}"><i class="bi bi-tools me-1"></i>Kerusakan</a></li>
            </ul>
            <ul class="navbar-nav">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link text-white" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell me-1"></i>
                        @if(\Auth::user()->unreadNotifications->count() > 0)
                            <span id="notif-badge" class="badge bg-danger rounded-pill" style="font-size:0.6rem;">{{ \Auth::user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width:320px;max-height:400px;overflow-y:auto;border-radius:14px;border:none;">
                        <li class="dropdown-header fw-bold">Notifikasi</li>
                        @forelse(\Auth::user()->unreadNotifications->take(10) as $notif)
                            <li>
                                <a class="dropdown-item small py-2 notification-item" 
                                   href="javascript:void(0)" 
                                   data-id="{{ $notif->id }}" 
                                   data-url="{{ $notif->data['url'] ?? (\Auth::user()->role == 'admin' ? route('admin.dashboard') : (\Auth::user()->role == 'mahasiswa' ? route('mahasiswa.dashboard') : route('petugas.dashboard'))) }}"
                                   style="cursor:pointer;">
                                    <strong>{{ $notif->data['judul'] ?? '' }}</strong><br>
                                    <span class="text-muted">{{ Str::limit($notif->data['pesan'] ?? '', 50) }}</span>
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
                    <a class="nav-link text-white d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width:30px;height:30px;font-size:0.75rem;font-weight:700;">{{ strtoupper(substr(\Auth::user()->mahasiswa?->nama ?? \Auth::user()->petugas?->nama ?? \Auth::user()->email, 0, 1)) }}</div>
                        <span class="d-none d-sm-inline small">{{ \Auth::user()->mahasiswa?->nama ?? \Auth::user()->petugas?->nama ?? explode('@', \Auth::user()->email)[0] }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius:14px;border:none;">
                        @if(\Auth::user()->role === 'admin')
                            <li class="px-3 py-2 border-bottom">
                                <strong class="d-block small">Administrator</strong>
                                <small class="text-muted">{{ \Auth::user()->email }}</small>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-2 text-primary"></i>Dashboard Admin</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.settings') }}"><i class="bi bi-gear me-2 text-secondary"></i>Pengaturan Akun</a></li>
                        @elseif(\Auth::user()->role === 'petugas')
                            <li class="px-3 py-2 border-bottom">
                                <strong class="d-block small">{{ \Auth::user()->petugas->nama ?? explode('@', \Auth::user()->email)[0] }}</strong>
                                <small class="text-muted">Petugas / Staff</small>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('petugas.dashboard') }}"><i class="bi bi-grid-fill me-2 text-primary"></i>Portal Petugas</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.settings') }}"><i class="bi bi-gear me-2 text-secondary"></i>Pengaturan Akun</a></li>
                        @else
                            <li class="px-3 py-2 border-bottom">
                                <strong class="d-block small">{{ \Auth::user()->mahasiswa->nama ?? \Auth::user()->email }}</strong>
                                <small class="text-muted">{{ \Auth::user()->email }}</small>
                                <div class="mt-1"><span class="badge bg-warning text-dark rounded-pill" style="font-size:0.7rem;"><i class="bi bi-star-fill me-1"></i>{{ \Auth::user()->poin ?? 0 }} Poin</span></div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('mahasiswa.dashboard') }}"><i class="bi bi-person-circle me-2 text-primary"></i>Dashboard</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.settings') }}"><i class="bi bi-gear me-2 text-secondary"></i>Pengaturan Akun</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li>
                    </ul>
                </li>
                @else
                    <li class="nav-item me-2"><a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">Masuk</a></li>
                    <li class="nav-item"><a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-3">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4 flex-grow-1">
    @yield('content')
</div>

<footer style="background:#0f172a;padding:40px 0 20px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('assets/logo.png') }}" height="32" class="rounded">
                    <span class="fw-bold text-white">Lost and Found PNP</span>
                </div>
                <p class="small mb-0" style="color:#94a3b8;line-height:1.7;">Platform digital Politeknik Negeri Padang untuk pelaporan barang hilang, temuan, dan kerusakan fasilitas kampus.</p>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white" style="font-size:0.85rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;">Navigasi</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('mahasiswa.kehilangan.index') }}" style="color:#94a3b8;text-decoration:none;">Kehilangan</a></li>
                    <li class="mb-2"><a href="{{ route('mahasiswa.temuan.index') }}" style="color:#94a3b8;text-decoration:none;">Temuan</a></li>
                    <li class="mb-2"><a href="{{ route('mahasiswa.kerusakan.index') }}" style="color:#94a3b8;text-decoration:none;">Kerusakan</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white" style="font-size:0.85rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;">Kontak</h6>
                <ul class="list-unstyled small" style="color:#94a3b8;">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Limau Manis, Padang 25176</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i><a href="tel:081292389075" style="color:inherit;text-decoration:none;">081292389075</a></li>
                    <li class="mb-2"><a href="https://pnp.ac.id" style="color:#94a3b8;text-decoration:none;"><i class="bi bi-globe me-2"></i>www.pnp.ac.id</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white" style="font-size:0.85rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;">Lokasi</h6>
                <a href="https://www.google.com/maps/place/Politeknik+Negeri+Padang/" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill w-100 mb-3" style="color:#94a3b8;border-color:#475569;">
                    <i class="bi bi-map me-1"></i>Buka di Google Maps
                </a>
                <div class="d-flex gap-2">
                    <a href="https://instagram.com/politekniknegeripadang_pnp" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#94a3b8;border-color:#475569;"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com/Pnp.ac.id" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#94a3b8;border-color:#475569;"><i class="bi bi-facebook"></i></a>
                    <a href="https://youtube.com/@politeknikkampuspadang" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#94a3b8;border-color:#475569;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        <hr style="border-color:#334155;" class="mt-4 mb-3">
        <p class="text-center small mb-0" style="color:#64748b;">&copy; {{ date('Y') }} <strong class="text-white">Politeknik Negeri Padang</strong>. Seluruh Hak Cipta Dilindungi.</p>
    </div>
</footer>

{{-- // modal lightbox --}}
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img src="" id="lightboxImg" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmAction(formId, title, text, icon = 'warning') {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#1e3a5f',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

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

document.addEventListener('DOMContentLoaded', function() {
    // Handle Notification Clicks
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const url = this.dataset.url;
            readNotif(id, url);
        });
    });

    // Handle Flash Messages
    const flashSuccess = "{{ session('success') }}";
    const flashError = "{{ session('error') }}";

    if (flashSuccess) {
        Swal.fire({ 
            icon: 'success', 
            title: 'Berhasil!', 
            text: flashSuccess, 
            timer: 3000, 
            showConfirmButton: false, 
            toast: true, 
            position: 'top-end' 
        });
    }
    if (flashError) {
        Swal.fire({ 
            icon: 'error', 
            title: 'Gagal!', 
            text: flashError, 
            timer: 4000, 
            showConfirmButton: true 
        });
    }

    // Initialize Lightbox
    const lightboxModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    const lightboxImg = document.getElementById('lightboxImg');

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('img-lightbox')) {
            lightboxImg.src = e.target.src;
            lightboxModal.show();
        }
    });
});
</script>
@stack('scripts')
</body>
</html>
