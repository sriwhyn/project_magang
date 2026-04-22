@extends(\Auth::user()->role === 'admin' ? 'layouts.app' : 'layouts.front')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Pengaturan Akun</h4>
                <p class="text-muted small">Kelola keamanan dan informasi akun Anda.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-shield-lock me-2"></i>Ganti Password</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" 
                                   placeholder="Masukkan password Anda saat ini" style="border-radius:12px; padding:12px 16px; background:#f8fafc;">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Masukkan password baru" style="border-radius:12px; padding:12px 16px; background:#f8fafc;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                   placeholder="Ulangi password baru Anda" style="border-radius:12px; padding:12px 16px; background:#f8fafc;">
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-4 rounded-3 bg-light border border-opacity-10 text-muted">
                <div class="d-flex gap-3">
                    <i class="bi bi-info-circle-fill text-primary" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tips Keamanan</h6>
                        <p class="small mb-0">Gunakan kombinasi huruf, angka, dan simbol untuk password yang lebih kuat. Jangan gunakan informasi yang mudah ditebak.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
