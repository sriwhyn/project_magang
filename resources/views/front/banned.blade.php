<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Diblokir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-danger text-white text-center py-4 border-0">
                        <i class="bi bi-exclamation-triangle-fill display-4 mb-2"></i>
                        <h4 class="mb-0 fw-bold">Akun Anda Diblokir</h4>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <p class="text-center text-muted mb-4">
                            Sistem mendeteksi adanya pelanggaran atau penipuan sehingga akun Anda {!! '<strong>' . \Auth::user()->email . '</strong>' !!} telah dinonaktifkan oleh Admin. Anda tidak dapat menggunakan fitur Lost and Found.
                        </p>

                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm rounded-3">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($banding)
                            <div class="alert alert-{{ $banding->status == 'menunggu' ? 'warning' : ($banding->status == 'disetujui' ? 'success' : 'danger') }} rounded-3 mt-4">
                                <h6 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Status Banding: {{ ucfirst($banding->status) }}</h6>
                                <p class="mb-0 small">Alasan Anda: "{{ $banding->alasan }}"</p>
                                @if($banding->status == 'ditolak')
                                    <hr>
                                    <p class="mb-0 small text-danger fw-bold">Pengajuan ditolak. Silakan ajukan ulang dengan alasan yang lebih jelas.</p>
                                @endif
                            </div>
                        @endif

                        @if(!$banding || $banding->status == 'ditolak')
                        <form action="{{ route('banned.store') }}" method="POST" class="mt-4 border-top pt-4">
                            @csrf
                            <h6 class="fw-bold mb-3"><i class="bi bi-envelope-paper me-2 text-primary"></i>Ajukan Pemulihan Akun</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Alasan Pengajuan</label>
                                <textarea name="alasan" class="form-control" rows="3" placeholder="Jelaskan mengapa Anda melanggar aturan dan berjanji tidak mengulanginya..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-pnp-primary w-100 rounded-pill fw-semibold shadow-sm">Kirim Pengajuan</button>
                        </form>
                        @endif
                    </div>

                    <div class="card-footer bg-white text-center py-3 border-0 mt-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-4">Kembali ke Beranda (Logout)</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
