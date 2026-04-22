<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Lost & Found PNP</title>
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
            padding: 3rem 0; color: var(--pnp-text);
        }
        
        .auth-card {
            background: #fff; border-radius: 32px;
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.08);
            overflow: hidden; display: flex; flex-direction: column;
            width: 100%; max-width: 1100px; margin: auto;
            border: 1px solid rgba(0,0,0,0.02);
        }

        @media (min-width: 768px) {
            .auth-card { flex-direction: row; min-height: 750px; }
            .brand-section { width: 32%; }
            .form-section { width: 68%; }
        }

        .brand-section {
            background: var(--pnp-gradient);
            padding: 60px 40px; color: white;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
            position: relative; overflow: hidden;
        }

        .brand-section img {
            height: 100px; margin-bottom: 25px;
            background: white; border-radius: 20px; padding: 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); z-index: 2;
        }

        .brand-section h2 { z-index: 2; font-weight: 800; letter-spacing: -0.04em; }

        .form-section { padding: 50px 70px; background: #fff; }

        .form-label { 
            font-size: 0.72rem; font-weight: 800; color: var(--pnp-muted); 
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; 
        }

        .form-control, .form-select {
            background: #fff; border: 2px solid var(--pnp-border); border-radius: 14px;
            padding: 12px 18px; font-size: 0.95rem; color: var(--pnp-text); transition: all 0.3s;
            font-weight: 500;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--pnp-blue);
            box-shadow: 0 8px 20px -10px rgba(59, 130, 246, 0.3);
            outline: none;
        }
        
        .section-title {
            position: relative; padding-left: 15px; margin: 25px 0;
            font-weight: 800; color: var(--pnp-text); font-size: 1rem;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .section-title::before {
            content: ""; position: absolute; left: 0; top: 4px; bottom: 4px;
            width: 4px; background: var(--pnp-blue); border-radius: 10px;
        }

        .role-fields { display: none; margin-bottom: 30px; animation: slideUp 0.4s ease-out; }
        .role-fields.active { display: block; }
        
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .ktm-upload-box {
            border: 2px dashed var(--pnp-border); border-radius: 20px;
            background: #f8fafc; padding: 25px; transition: all 0.3s;
            text-align: center; cursor: pointer; min-height: 180px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .ktm-upload-box:hover { border-color: var(--pnp-blue); background: #fff; }
        .ktm-upload-box img { max-width: 100%; max-height: 250px; object-fit: contain; border-radius: 12px; }

        .btn-primary {
            background: var(--pnp-gradient);
            border: none; border-radius: 16px; padding: 18px; font-weight: 800; font-size: 1.1rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3); transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.4); }

        .footer-link { margin-top: 30px; text-align: center; font-size: 0.95rem; color: var(--pnp-muted); }
        .footer-link a { color: var(--pnp-blue); text-decoration: none; font-weight: 800; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="brand-section">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                <h2 class="fw-bold fs-3">Lost & Found</h2>
            </div>

            <div class="form-section">
                <div class="mb-5">
                    <h4 class="fw-bold text-dark mb-1">Buat Akun Baru</h4>
                    <p class="text-muted small">Silakan lengkapi identitas resmi Anda</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small rounded-4 p-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Row 1: Role & Nama -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <label class="form-label">Tipe Pengguna</label>
                            <select class="form-select" name="role" id="roleSelect" required>
                                <option value="mahasiswa" selected>Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Andi Pratama">
                        </div>
                    </div>

                    <!-- Area Mahasiswa (KTM & NIM) -->
                    <div id="fields-mahasiswa" class="role-fields active">
                        <h6 class="section-title">Dokumen Mahasiswa</h6>
                        <div class="row g-4 align-items-center">
                            <div class="col-md-7">
                                <label class="form-label">Foto Kartu Tanda Mahasiswa (KTM)</label>
                                <div class="ktm-upload-box" onclick="document.getElementById('inputKtm').click()">
                                    <input type="file" class="d-none" name="foto_ktm" id="inputKtm" accept="image/*">
                                    <div id="ktmPreviewContent">
                                        <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50"></i>
                                        <p class="text-muted small mt-2 mb-0">Klik untuk unggah foto KTM asli</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                                <input type="text" class="form-control" name="nim" value="{{ old('nim') }}" placeholder="221108...">
                                <div class="form-text mt-3 small">Admin akan memverifikasi kesesuaian NIM dengan foto KTM.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Area Dosen -->
                    <div id="fields-dosen" class="role-fields">
                        <h6 class="section-title">Akses Pengajar</h6>
                        <label class="form-label">Nomor Induk Pegawai (NIP)</label>
                        <input type="text" class="form-control" name="nip" value="{{ old('nip') }}" placeholder="18 digit angka NIP..." maxlength="18" minlength="18" pattern="[0-9]{18}">
                        <div class="form-text mt-1 small">NIP harus terdiri dari 18 digit angka tanpa spasi.</div>
                        
                        <div class="mt-3">
                            <label class="form-label">Foto Kartu Pegawai (Asli)</label>
                            <input type="file" class="form-control" name="foto_id" accept="image/*">
                            <div class="form-text mt-1 small text-primary fw-bold"><i class="bi bi-info-circle me-1"></i> Digunakan untuk proses verifikasi akun oleh admin.</div>
                        </div>
                    </div>

                    <!-- Row 2: Informasi Akademik (Jurusan & Prodi) -->
                    <h6 class="section-title">Informasi Akademik</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Jurusan</label>
                            <select class="form-select" name="jurusan" id="jurusanSelect" required>
                                <option value="" disabled selected>Pilih Jurusan...</option>
                                <option value="Teknologi Informasi">Teknologi Informasi</option>
                                <option value="Teknik Elektro">Teknik Elektro</option>
                                <option value="Teknik Mesin">Teknik Mesin</option>
                                <option value="Teknik Sipil">Teknik Sipil</option>
                                <option value="Akuntansi">Akuntansi</option>
                                <option value="Administrasi Niaga">Administrasi Niaga</option>
                                <option value="Bahasa Inggris">Bahasa Inggris</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Studi / Prodi</label>
                            <select class="form-select" name="prodi" id="prodiSelect" required>
                                <option value="" disabled selected>Pilih Jurusan Terlebih Dahulu...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Email & Kontak -->
                    <h6 class="section-title">Hubungi & Login</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Email Kampus / Umum</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="user@pnp.ac.id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxx">
                        </div>
                    </div>

                    <!-- Row 4: Password -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Kata Sandi</label>
                            <input type="password" class="form-control" name="password" required placeholder="Pasang sandi kuat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ulangi Sandi</label>
                            <input type="password" class="form-control" name="password_confirmation" required placeholder="Ketik ulang sandi">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow-lg">
                        Selesaikan Pendaftaran <i class="bi bi-check-all ms-1"></i>
                    </button>
                    
                    <div class="footer-link">
                        Sudah punya akun? <a href="{{ url('/login') }}">Masuk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const roleSelect = document.getElementById('roleSelect');
    const inputKtm = document.getElementById('inputKtm');
    const ktmPreviewContent = document.getElementById('ktmPreviewContent');
    const jurusanSelect = document.getElementById('jurusanSelect');
    const prodiSelect = document.getElementById('prodiSelect');

    const prodiData = {
        "Teknik Sipil": [
            "D3 Teknik Sipil",
            "D4 Teknik Perancangan Jalan dan Jembatan",
            "D4 Manajemen Rekayasa Konstruksi"
        ],
        "Teknik Mesin": [
            "D3 Teknik Mesin", 
            "D3 Teknik Alat Berat", 
            "D4 Teknik Manufaktur"
        ],
        "Teknik Elektro": [
            "D3 Teknik Listrik", 
            "D3 Teknik Elektronika", 
            "D3 Teknik Telekomunikasi", 
            "D4 Teknik Elektronika",
            "D4 Teknik Telekomunikasi",
            "D4 Teknik Elektro Industri"
        ],
        "Administrasi Niaga": [
            "D3 Administrasi Bisnis", 
            "D3 Usaha Perjalanan Wisata",
            "D4 Destinasi Pariwisata",
            "D4 Manajemen Bisnis Internasional"
        ],
        "Akuntansi": [
            "D3 Akuntansi", 
            "D4 Akuntansi Manajerial"
        ],
        "Teknologi Informasi": [
            "D3 Manajemen Informatika", 
            "D3 Teknik Komputer", 
            "D4 Teknologi Rekayasa Perangkat Lunak"
        ],
        "Bahasa Inggris": [
            "D3 Bahasa Inggris"
        ]
    };

    function toggleFields() {
        document.querySelectorAll('.role-fields').forEach(el => el.classList.remove('active'));
        const target = document.getElementById('fields-' + roleSelect.value);
        if (target) target.classList.add('active');
    }

    function updateProdi() {
        const selectedJurusan = jurusanSelect.value;
        const prodis = prodiData[selectedJurusan] || [];
        
        prodiSelect.innerHTML = '<option value="" disabled selected>Pilih Program Studi...</option>';
        
        prodis.forEach(prodi => {
            const option = document.createElement('option');
            option.value = prodi;
            option.text = prodi;
            prodiSelect.appendChild(option);
        });
    }

    roleSelect.addEventListener('change', toggleFields);
    jurusanSelect.addEventListener('change', updateProdi);
    
    toggleFields();

    inputKtm.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            ktmPreviewContent.innerHTML = `<img src="${ev.target.result}" alt="KTM Preview">`;
        };
        reader.readAsDataURL(file);
    });
</script>
</body>
</html>
