<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Lost & Found PNP')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --pnp-navy: #001D39;
            --pnp-blue: #0A4174;
            --pnp-accent: #7BBDE8;
            --pnp-gradient: linear-gradient(135deg, #001D39 0%, #08335b 100%);
            --pnp-bg: #E3EDF5;
            --pnp-card-bg: #ffffff;
            --pnp-text: #001D39;
            --pnp-muted: #49769F;
            --pnp-border: #91b5ce;
            --pnp-sidebar: 260px;
            --pnp-radius: 1rem;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--pnp-bg); 
            color: var(--pnp-text); 
            letter-spacing: -0.01em; 
            overflow-x: hidden;
        }

        /* Modern Sidebar */
        .sidebar {
            background: var(--pnp-gradient);
            width: var(--pnp-sidebar);
            height: 100vh;
            position: fixed; top: 0; left: 0;
            z-index: 1050;
            padding: 1.5rem 1rem;
            transition: all 0.3s ease;
            display: flex; flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .sidebar .brand {
            padding: 1rem 0.5rem 2.5rem;
            font-weight: 800;
            color: white;
            font-size: 1.25rem;
            display: flex; align-items: center; gap: 12px;
        }

        .sidebar .nav-group-label {
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.5rem 1rem 0.6rem;
        }

        .sidebar .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            font-weight: 600;
            font-size: 0.88rem;
            border-radius: 12px;
            margin-bottom: 4px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover { color: white; background: rgba(255,255,255,0.08); }
        .sidebar .nav-link.active { background: var(--pnp-blue); color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); }

        /* Top Bar */
        .top-navbar {
            height: 72px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--pnp-border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            position: sticky; top: 0; z-index: 1000;
        }

        .main-wrapper { margin-left: var(--pnp-sidebar); min-height: 100vh; }
        .content-body { padding: 2.5rem; }

        .admin-card {
            background: white; border-radius: var(--pnp-radius);
            border: 1px solid var(--pnp-border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 1.5rem;
        }

        .btn-pnp-outline {
            border: 1px solid var(--pnp-border);
            color: var(--pnp-text);
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .btn-pnp-outline:hover { background: #f1f5f9; border-color: #cbd5e1; }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }

        /* Shared Admin Form Styles */
        .form-card-simple {
            background: white;
            border-radius: var(--pnp-radius);
            border: 1px solid var(--pnp-border);
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        .form-section-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--pnp-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 2rem;
            display: flex; align-items: center; gap: 10px;
        }

        .form-section-label::after {
            content: ""; flex: 1; height: 1px; background: var(--pnp-border);
        }

        .form-group-custom { margin-bottom: 1.5rem; }
        .form-group-custom label { font-weight: 700; color: var(--pnp-text); margin-bottom: 8px; font-size: 0.85rem; }

        .input-simple {
            border-radius: 12px;
            border: 2px solid var(--pnp-border);
            padding: 12px 16px;
            font-weight: 500;
            background: #fff;
            transition: all 0.2s;
        }

        .input-simple:focus {
            border-color: var(--pnp-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- // sidebar --}}
<aside class="sidebar d-none d-lg-block">
    <div class="brand">
        <span>Lost and Found</span>
    </div>

    <div class="nav-group-label">Menu Utama</div>
    <nav class="nav flex-column">
        @php
            $dashboardRoute = \Auth::user()->role == 'admin' ? route('admin.dashboard') : route('petugas.dashboard');
            $dashboardActive = request()->routeIs('admin.dashboard') || request()->routeIs('petugas.dashboard');
        @endphp
        <a class="nav-link {{ $dashboardActive ? 'active' : '' }}" href="{{ $dashboardRoute }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        @if(\Auth::user()->role === 'admin')
        <a class="nav-link {{ request()->routeIs('petugas.kehilangan.*') ? 'active' : '' }}" href="{{ route('petugas.kehilangan.index') }}">
            <i class="bi bi-search"></i> Kehilangan
        </a>
        <a class="nav-link {{ request()->routeIs('petugas.temuan.*') ? 'active' : '' }}" href="{{ route('petugas.temuan.index') }}">
            <i class="bi bi-box-seam"></i> Barang Temuan
        </a>
        <a class="nav-link {{ request()->routeIs('petugas.klaim.*') ? 'active' : '' }}" href="{{ route('petugas.klaim.index') }}">
            <i class="bi bi-check2-circle"></i> Klaim Barang
        </a>
        @endif
        <a class="nav-link {{ request()->routeIs('petugas.kerusakan.*') ? 'active' : '' }}" href="{{ route('petugas.kerusakan.index') }}">
            <i class="bi bi-tools"></i> Kerusakan
        </a>
    </nav>

    @if(\Auth::user()->role == 'admin')
    <div class="nav-group-label mt-4">Manajemen Sistem</div>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" href="{{ route('admin.kategori.index') }}">
            <i class="bi bi-tags"></i> Kategori
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.verify.index') ? 'active' : '' }} d-flex justify-content-between align-items-center" href="{{ route('admin.users.verify.index') }}">
            <span><i class="bi bi-shield-exclamation"></i> Audit Akun</span>
            @php $pendingCount = \App\Models\User::whereIn('role', ['mahasiswa', 'dosen'])->where('status_akun', 'nonaktif')->count(); @endphp
            @if($pendingCount > 0)
                <span class="badge rounded-pill bg-danger" style="font-size: 0.65rem;">{{ $pendingCount }}</span>
            @endif
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.mahasiswa') ? 'active' : '' }}" href="{{ route('admin.users.mahasiswa') }}">
            <i class="bi bi-people"></i> Mahasiswa
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.dosen') ? 'active' : '' }}" href="{{ route('admin.users.dosen') }}">
            <i class="bi bi-person-workspace"></i> Dosen
        </a>
        <a class="nav-link {{ request()->routeIs('admin.akun_petugas.*') ? 'active' : '' }}" href="{{ route('admin.akun_petugas.index') }}">
            <i class="bi bi-person-badge"></i> Staf Petugas
        </a>
    </nav>
    @endif

    <div class="mt-auto pt-3 border-top border-secondary border-opacity-10">
        @if(\Auth::user()->role !== 'admin')
        <a class="nav-link text-dark mb-2" href="{{ route('profile.settings') }}">
            <i class="bi bi-gear"></i> Pengaturan Akun
        </a>
        @endif
        <a class="nav-link text-primary mb-2" href="{{ route('home') }}">
            <i class="bi bi-globe"></i> Kunjungi Website
        </a>
        <a class="nav-link text-danger opacity-75" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</aside>

<!-- // wrapper utama -->
<div class="main-wrapper">
    {{-- // navigasi atas --}}
    <header class="top-navbar">
        <div></div>
        
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-none d-md-flex align-items-center gap-2" style="font-weight: 600; font-size: 0.8rem;">
                <i class="bi bi-globe"></i> Lihat Website
            </a>

            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-2" style="font-weight: 600; font-size: 0.8rem;">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>

            {{-- // dropdown notif --}}
            <div class="dropdown">
                <div class="position-relative p-2 rounded-circle dropdown-toggle no-caret" id="dropdownNotif" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer; width:38px; height:38px; display:flex; align-items:center; justify-content:center; transition: background 0.2s;">
                    <i class="bi bi-bell text-muted"></i>
                    @php $unreadCount = \Auth::user()->unreadNotifications->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.65rem; padding: 4px 6px; border: 2px solid white;">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </div>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-3 py-0 overflow-hidden" aria-labelledby="dropdownNotif" style="width: 320px;">
                    <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Notifikasi</h6>
                        @if(\Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.readAll') }}" class="text-primary small text-decoration-none fw-semibold">Tandai Semua Dibaca</a>
                        @endif
                    </div>
                    <div class="notification-list" style="max-height: 350px; overflow-y: auto;">
                        @forelse(\Auth::user()->unreadNotifications->take(10) as $notif)
                            <a href="{{ $notif->data['url'] ?? '#' }}" 
                               data-id="{{ $notif->id }}" 
                               data-url="{{ $notif->data['url'] ?? '#' }}"
                               class="dropdown-item p-3 border-bottom d-flex gap-3 align-items-start whitespace-normal btn-mark-read">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div style="white-space: normal;">
                                    <div class="fw-bold text-dark small">{{ $notif->data['judul'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem; line-height: 1.4;">{{ $notif->data['pesan'] }}</div>
                                    <div class="text-primary mt-1" style="font-size: 0.65rem; font-weight: 600;">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center">
                                <i class="bi bi-bell-slash text-muted display-6 d-block mb-2"></i>
                                <span class="text-muted small">Tidak ada notifikasi baru</span>
                            </div>
                        @endforelse
                    </div>
                    @if(\Auth::user()->notifications->count() > 0)
                        <div class="p-2 border-top text-center bg-light">
                            <a href="{{ route('notifications.index') }}" class="text-muted small text-decoration-none fw-semibold">Lihat Semua Laporan</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3 ps-3 border-start">
                <div class="text-end d-none d-sm-block">
                    @php
                        $userName = \Auth::user()->role === 'admin' ? (\Auth::user()->admin->nama ?? \Auth::user()->email) : (\Auth::user()->petugas->nama ?? \Auth::user()->email);
                        $userRole = ucfirst(\Auth::user()->role);
                    @endphp
                    <div class="fw-bold" style="font-size: 0.85rem; color: var(--admin-text-main);">{{ $userName }}</div>
                    <div class="text-muted" style="font-size:0.7rem; font-weight: 500;">{{ $userRole }}</div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width:36px;height:36px;overflow:hidden;border:1px solid #91b5ce;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=f1f5f9&color=0A4174&bold=true" alt="Avatar" class="w-100 h-100">
                </div>
            </div>
        </div>
    </header>


    {{-- // konten halaman --}}
    <main class="content-body">
        @yield('content')
    </main>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = @json(session('success'));
            const errorMsg = @json(session('error'));

            if (successMsg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: successMsg,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            if (errorMsg) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMsg,
                    showConfirmButton: true
                });
            }

            // Notif read handler
            document.querySelectorAll('.btn-mark-read').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const url = this.getAttribute('data-url');
                    markAsRead(e, id, url);
                });
            });
        });

        function confirmAction(formId, title, text, icon = 'question') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    if(form) form.submit();
                }
            });
        }
        function markAsRead(event, id, url) {
            event.preventDefault();
            fetch(`/notifications/${id}/read`)
                .then(() => {
                    window.location.href = url;
                })
                .catch(() => {
                    window.location.href = url;
                });
        }
    </script>
    <style>
        .no-caret::after { display: none !important; }
        .dropdown-item:active { background-color: var(--pnp-blue); }
    </style>
@stack('scripts')
</body>
</html>
