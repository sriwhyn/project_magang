<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Lost & Found PNP</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --pnp-blue: #0A4174;
            --pnp-navy: #001D39;
            --dark-bg: #000c18;
            --card-bg: #001D39;
            --input-bg: #0a4174;
            --text-main: #BDD8E9;
            --text-muted: #7BBDE8;
            --border-light: rgba(255,255,255,0.1);
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--dark-bg);
            background-image: radial-gradient(circle at top right, rgba(10, 65, 116, 0.25), transparent 40%),
                              radial-gradient(circle at bottom left, rgba(0, 29, 57, 0.8), transparent 40%);
            min-height: 100vh; display: flex; align-items: center; 
            padding: 2rem 0;
            color: var(--text-main);
        }
        
        .auth-card {
            background: var(--card-bg); border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.05);
            overflow: hidden; display: flex; flex-direction: column;
            width: 100%; max-width: 950px; margin: auto;
        }

        @media (min-width: 768px) {
            .auth-card { flex-direction: row; min-height: 600px; }
            .brand-section { width: 42%; }
            .form-section { width: 58%; }
        }

        .brand-section {
            background: linear-gradient(135deg, #000c18 0%, #001D39 100%);
            border-right: 1px solid var(--border-light);
            padding: 60px 40px; color: white;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
            position: relative; overflow: hidden;
        }

        /* Subtle Geometric Pattern */
        .brand-section::before {
            content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.03) 1px, transparent 0);
            background-size: 24px 24px;
        }

        .brand-section img {
            height: 110px; margin-bottom: 30px;
            background: white; border-radius: 24px; padding: 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            z-index: 2; transform: rotate(-2deg); transition: 0.3s;
        }
        .brand-section img:hover { transform: rotate(0deg) scale(1.05); }

        .brand-section h2 { z-index: 2; font-weight: 800; letter-spacing: -0.03em; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
        .brand-section p { z-index: 2; color: var(--text-muted); font-size: 0.9rem; }

        .form-section { padding: 60px; background: transparent; display: flex; flex-direction: column; justify-content: center; }

        .form-label { 
            font-size: 0.75rem; font-weight: 800; color: #cbd5e1; 
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; 
            display: flex; align-items: center; gap: 6px;
        }

        .input-group-custom {
            position: relative; margin-bottom: 24px;
        }

        .form-control {
            background: var(--input-bg); border: 1px solid var(--border-light); border-radius: 12px;
            padding: 14px 20px; font-size: 1rem; color: #fff; transition: all 0.3s;
            font-weight: 500;
        }
        .form-control::placeholder { color: #64748b; }
        .form-control:focus {
            background: #1e293b; border-color: var(--pnp-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            outline: none; color: #fff;
        }

        .btn-primary {
            background: var(--pnp-blue);
            border: none; border-radius: 12px; padding: 16px; font-weight: 700; font-size: 1.05rem; color: #fff;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            background: #2563eb; color: #fff;
        }

        .footer-links { margin-top: 40px; text-align: center; }
        .footer-links p { color: var(--text-muted); font-size: 0.95rem; }
        .footer-links a { color: var(--pnp-blue); text-decoration: none; font-weight: 700; transition: 0.2s; }
        .footer-links a:hover { color: #60a5fa; }
        
        h4 { font-weight: 800; letter-spacing: -0.02em; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="brand-section">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                <h2 class="mb-1">Lost & Found</h2>
                <p>Politeknik Negeri Padang</p>
            </div>

            <div class="form-section">
                <div class="mb-5">
                    <h4 class="mb-2">Login Admin <i class="bi bi-shield-lock-fill text-primary ms-1"></i></h4>
                    <p style="color: var(--text-muted)">Silakan login untuk masuk ke dasbor admin.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border border-danger bg-danger bg-opacity-10 text-danger small rounded-3 p-3 mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    <div class="input-group-custom">
                        <label class="form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@domain.com">
                    </div>

                    <div class="input-group-custom">
                        <div class="d-flex justify-content-between">
                            <label class="form-label"><i class="bi bi-key-fill"></i> Password</label>
                        </div>
                        <input type="password" class="form-control" name="password" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        Masuk <i class="bi bi-box-arrow-in-right ms-2 fs-5"></i>
                    </button>
                </form>

                <div class="footer-links">
                    <p><a href="/"><i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
