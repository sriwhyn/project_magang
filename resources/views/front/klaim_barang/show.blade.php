@extends('layouts.front')
@section('title', 'Detail Klaim Barang')

@section('content')
@push('styles')
<style>
    .premium-card { border: none; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,29,57,0.08); overflow: hidden; background: white; }
    .premium-header { 
        background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); 
        padding: 2.5rem 2rem; 
        position: relative;
    }
    .status-badge-top {
        display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 50px;
        font-weight: 800; font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase;
        margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .detail-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.25rem; }
    @media (min-width: 768px) { .detail-grid { grid-template-columns: repeat(2, 1fr); } }
    
    .info-item { background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; height: 100%; transition: all 0.2s; }
    .info-item:hover { border-color: #0A4174; background: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .info-label { font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-value { font-size: 0.95rem; font-weight: 700; color: #0f172a; word-break: break-word; }
    
    .section-title { font-size: 0.8rem; font-weight: 900; color: #001D39; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="col-lg-10 mx-auto">
        <div class="mb-4">
            <a href="{{ route('mahasiswa.klaim.index') }}" class="btn btn-link text-muted text-decoration-none p-0 small fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-circle-fill fs-5"></i> KEMBALI KE DAFTAR
            </a>
        </div>

        @php 
            $statusColor = match($klaimBarang->status_klaim) { 'menunggu' => '#856404', 'disetujui' => '#155724', 'ditolak' => '#721c24', default => '#383d41' };
            $statusBg = match($klaimBarang->status_klaim) { 'menunggu' => '#fff3cd', 'disetujui' => '#d4edda', 'ditolak' => '#f8d7da', default => '#e2e3e5' };
            $isClaimant = \Auth::check() && ($klaimBarang->id_user_pengaju == \Auth::id());
        @endphp

        <div class="premium-card mb-5">
            {{-- Header --}}
            <div class="premium-header text-center">
                <div class="status-badge-top" style="background-color: {{ $statusBg }}; color: {{ $statusColor }};">
                    <i class="bi bi-shield-check"></i> KLAIM #K{{ $klaimBarang->id_klaim }} <span class="mx-2">|</span> {{ strtoupper($klaimBarang->status_klaim) }}
                </div>
                <h1 class="display-6 fw-900 text-white mb-2 text-uppercase" style="letter-spacing: -1px;">{{ $klaimBarang->barangTemuan->nama_barang ?? 'DETAIL KLAIM' }}</h1>
                <p class="mb-0 small fw-bold text-white-50 opacity-75">
                    DIAJUKAN PADA: {{ $klaimBarang->created_at->format('d M Y') }} <span class="mx-2">|</span> 
                    OLEH: {{ $klaimBarang->nama_pemilik }}
                </p>
            </div>

            <div class="p-4 p-md-5">
                {{-- SECTION: STATUS PROGRESS --}}
                <div class="bg-light p-4 p-md-5 rounded-4 border mb-5">
                    <h6 class="section-title"><i class="bi bi-activity text-primary"></i> Progres Verifikasi Klaim</h6>
                    @if($isClaimant)
                    <div class="sc-stepper mb-0 px-lg-5">
                        <div class="sc-step completed">
                            <div class="sc-step-icon"><i class="bi bi-check2"></i></div>
                            <div class="sc-step-label">Diajukan</div>
                        </div>
                        <div class="sc-step {{ $klaimBarang->status_klaim != 'menunggu' ? 'completed' : 'active' }}">
                            <div class="sc-step-icon">@if($klaimBarang->status_klaim != 'menunggu')<i class="bi bi-check2"></i>@else 2 @endif</div>
                            <div class="sc-step-label">Ditinjau Admin</div>
                        </div>
                        @php $isDone = in_array($klaimBarang->status_klaim, ['disetujui', 'ditolak']); @endphp
                        <div class="sc-step {{ $isDone ? 'completed' : ($klaimBarang->status_klaim != 'menunggu' ? 'active' : '') }}">
                            <div class="sc-step-icon">@if($isDone)<i class="bi bi-check2"></i>@else 3 @endif</div>
                            <div class="sc-step-label">Hasil Akhir</div>
                        </div>
                    </div>
                    @else
                        <div class="text-center py-2">
                            <span class="badge bg-opacity-10 px-4 py-2 rounded-pill fw-800" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}33;">
                                {{ strtoupper($klaimBarang->status_klaim) }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <h6 class="section-title"><i class="bi bi-file-earmark-text-fill text-primary"></i> Identifikasi Kepemilikan</h6>
                        <div class="detail-grid">
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-tag"></i>Kategori</span>
                                <span class="info-value">{{ $klaimBarang->kategori->nama_kategori ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-palette"></i>Warna Barang</span>
                                <span class="info-value">{{ $klaimBarang->warna ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-event"></i>Tanggal Hilang</span>
                                <span class="info-value">{{ $klaimBarang->tanggal_hilang ? date('d M Y', strtotime($klaimBarang->tanggal_hilang)) : '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-geo-alt"></i>Lokasi Kehilangan</span>
                                <span class="info-value">{{ $klaimBarang->lokasi_hilang }}</span>
                            </div>
                            @if($klaimBarang->skor_kecocokan)
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-cpu"></i>Skor Kecocokan</span>
                                <span class="info-value text-primary">{{ $klaimBarang->skor_kecocokan }}%</span>
                            </div>
                            @endif
                            <div class="info-item col-12">
                                <span class="info-label"><i class="bi bi-chat-left-text"></i>Ciri Khusus / Deskripsi</span>
                                <p class="mb-0 info-value fw-medium" style="line-height:1.5;">{{ $klaimBarang->deskripsi_ciri_khusus }}</p>
                            </div>
                        </div>

                        @if($klaimBarang->catatan_admin)
                        <div class="mt-4 p-4 rounded-4 border-start border-4 border-primary bg-light shadow-sm">
                            <span class="info-label text-primary"><i class="bi bi-chat-quote-fill"></i> Catatan Dari Admin</span>
                            <p class="mb-0 fw-bold mt-1" style="color: #001D39;">"{{ $klaimBarang->catatan_admin }}"</p>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-5">
                        <h6 class="section-title"><i class="bi bi-camera-fill text-primary"></i> Bukti Lampiran</h6>
                        @if($klaimBarang->foto_bukti)
                            <div class="rounded-4 overflow-hidden border shadow-sm">
                                <img src="{{ asset('storage/' . $klaimBarang->foto_bukti) }}" class="img-fluid w-100 img-lightbox" style="max-height: 400px; object-fit: cover; cursor: pointer;" alt="Bukti">
                            </div>
                        @else
                            <div class="text-center p-5 border border-dashed rounded-4 bg-light">
                                <i class="bi bi-image text-muted display-6 mb-3 opacity-25"></i>
                                <p class="extra-small text-muted fw-bold mb-0 text-uppercase">Tidak ada foto bukti terlampir.</p>
                            </div>
                        @endif
                        
                        @if($klaimBarang->status_klaim == 'disetujui')
                            <div class="alert alert-success mt-4 rounded-4 border-2 shadow-sm animate-pulse">
                                <p class="fw-bold mb-1 small"><i class="bi bi-check-circle-fill me-2"></i>SELAMAT!</p>
                                <p class="mb-0 small">Klaim Anda **disetujui**. Silakan segera hubungi petugas di Kantor Admin untuk proses pengambilan fisik barang.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light p-4 text-center border-top">
                <small class="text-muted fw-bold text-uppercase letter-spacing-1">Sistem Lost & Found PNP &bull; Update: {{ $klaimBarang->updated_at->format('d M Y H:i') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection