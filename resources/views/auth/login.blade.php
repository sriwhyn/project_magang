<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Lost & Found PNP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --pnp-navy: #0f172a;
            --pnp-blue: #3b82f6;
            --pnp-gradient: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            --pnp-bg: #f8fafc;
            --pnp-text: #1e293b;
            --pnp-muted: #64748b;
            --pnp-border: #e2e8f0;
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--pnp-bg);
            min-height: 100vh; display: flex; align-items: center; 
            padding: 2rem 0; color: var(--pnp-text);
        }
        
        .auth-card {
            background: #fff; border-radius: 32px;
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.08);
            overflow: hidden; display: flex; flex-direction: column;
            width: 100%; max-width: 950px; margin: auto;
            border: 1px solid rgba(0,0,0,0.02);
        }

        @media (min-width: 768px) {
            .auth-card { flex-direction: row; min-height: 600px; }
            .brand-section { width: 42%; }
            .form-section { width: 58%; }
        }

        .brand-section {
            background: var(--pnp-gradient);
            padding: 60px 40px; color: white;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
            position: relative; overflow: hidden;
        }

        .brand-section img {
            height: 110px; margin-bottom: 30px;
            background: white; border-radius: 24px; padding: 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            z-index: 2; transform: rotate(-2deg); transition: 0.3s;
        }
        .brand-section img:hover { transform: rotate(0deg) scale(1.05); }

        .brand-section h2 { z-index: 2; font-weight: 800; letter-spacing: -0.04em; }

        .form-section { padding: 60px; background: #fff; display: flex; flex-direction: column; justify-content: center; }

        .form-label { 
            font-size: 0.75rem; font-weight: 800; color: var(--pnp-muted); 
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; 
            display: flex; align-items: center; gap: 6px;
        }

        .input-group-custom {
            position: relative; margin-bottom: 24px;
        }

        .form-control {
            background: #fff; border: 2px solid var(--pnp-border); border-radius: 16px;
            padding: 14px 20px; font-size: 1rem; color: var(--pnp-text); transition: all 0.3s;
            font-weight: 500;
        }
        .form-control:focus {
            border-color: var(--pnp-blue);
            box-shadow: 0 10px 20px -10px rgba(59, 130, 246, 0.3);
            outline: none;
        }

        .btn-primary {
            background: var(--pnp-gradient);
            border: none; border-radius: 16px; padding: 16px; font-weight: 800; font-size: 1.05rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
        }
        .btn-primary:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.4);
        }

        .footer-links { margin-top: 40px; text-align: center; }
        .footer-links p { color: var(--pnp-muted); font-size: 0.95rem; }
        .footer-links a { color: var(--pnp-blue); text-decoration: none; font-weight: 800; transition: 0.2s; }
        .footer-links a:hover { color: var(--pnp-blue); border-bottom: 2px solid var(--pnp-blue); }
        
        h4 { font-weight: 800; letter-spacing: -0.02em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="brand-section">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                <h2 class="mb-0">Lost & Found</h2>
            </div>

            <div class="form-section">
                <div class="mb-5">
                    <h4 class="text-dark mb-2">Selamat Datang Kembali</h4>
                    <p class="text-muted">Masuk untuk mengelola laporan barang Anda</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success small rounded-4 p-3 mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small rounded-4 p-3 mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}">
                    @csrf
                    <div class="input-group-custom">
                        <label class="form-label"><i class="bi bi-envelope-fill"></i> Email Kampus</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@pnp.ac.id">
                    </div>

                    <div class="input-group-custom">
                        <div class="d-flex justify-content-between">
                            <label class="form-label"><i class="bi bi-lock-fill"></i> Kata Sandi</label>
                        </div>
                        <input type="password" class="form-control" name="password" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Masuk ke Portal <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                    </button>
                </form>

                <div class="footer-links">
                    <p>Belum memiliki akun? <a href="{{ url('/register') }}">Daftar Sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
