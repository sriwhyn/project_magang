@extends('layouts.front')
@push('styles')
<style>
    .premium-card { border: none; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,29,57,0.08); overflow: hidden; background: white; }
    .premium-header { 
        background: linear-gradient(135deg, #001D39 0%, #0A4174 100%); 
        padding: 2.5rem 2rem; 
        position: relative;
    }
    .premium-header h1 { color: #ffffff !important; font-weight: 900; }
    .premium-header p { color: rgba(255,255,255,0.7) !important; font-weight: 600; }
    .status-badge-top {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.7rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .info-item { background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; transition: all 0.2s; height: 100%; }
    .info-item:hover { border-color: #0A4174; background: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .info-label { font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-value { font-size: 0.95rem; font-weight: 700; color: #0f172a; word-break: break-word; }
    
    .status-alert { border-radius: 20px; padding: 1.5rem; border: 1px solid #dbeafe; background: #f0f7ff; }
    .img-premium { width: 100%; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s; }
    .img-premium:hover { transform: scale(1.02); }
    
    .section-title { font-size: 0.85rem; font-weight: 900; color: #001D39; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 10px; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="col-lg-10 mx-auto">
        {{-- // kembali --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('mahasiswa.kerusakan.index') }}" class="btn btn-link text-muted text-decoration-none p-0 small fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-circle-fill fs-5"></i> KEMBALI
            </a>
            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-800" style="font-size: 0.7rem; letter-spacing: 1px;">MODUL PELAPORAN</div>
        </div>

        <div class="premium-card">
            @php
                $status = $laporanKerusakan->status_perbaikan ?? 'dilaporkan';
                $isDiproses = in_array($status, ['diproses', 'ditugaskan', 'dalam_pengerjaan', 'selesai']);
                $isDalamKerja = in_array($status, ['dalam_pengerjaan', 'selesai']);
                $isSelesai = ($status == 'selesai');
            @endphp

            {{-- // banner --}}
            <div class="premium-header text-center">
                <div class="status-badge-top bg-white text-primary">
                    <i class="bi bi-tools"></i> #R{{ $laporanKerusakan->id_laporan }} <span class="mx-2">|</span> {{ strtoupper(str_replace('_', ' ', $status)) }}
                </div>
                <h1 class="display-6 fw-900 text-white mb-2 text-uppercase" style="letter-spacing: -1px;">{{ $laporanKerusakan-> judul_laporan }}</h1>
                <p class="mb-0 small fw-bold text-white-50 opacity-75">
                    DILAPORKAN PADA: {{ $laporanKerusakan->created_at->format('d M Y') }} <span class="mx-2">|</span> 
                    KATEGORI: {{ strtoupper($laporanKerusakan->kategori->nama_kategori) }}
                </p>
            </div>

            <div class="p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-7">
                        <h6 class="section-title"><i class="bi bi-info-circle-fill text-primary"></i> Informasi Kerusakan</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-geo-alt"></i>Lokasi Fasilitas</span>
                                    <span class="info-value text-primary fs-5">{{ $laporanKerusakan->lokasi_kerusakan }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="info-label"><i class="bi bi-chat-left-text"></i>Detail Keluhan & Deskripsi</span>
                                    <p class="mb-0 info-value fw-medium" style="line-height: 1.6;">{{ $laporanKerusakan->deskripsi }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- // foto --}}
                        @if($laporanKerusakan->gambar)
                        <div>
                            <h6 class="section-title"><i class="bi bi-camera-fill text-primary"></i> Foto Kondisi Fisik</h6>
                            <div class="row g-2">
                                @php $fotos = is_array($laporanKerusakan->gambar) ? $laporanKerusakan->gambar : json_decode($laporanKerusakan->gambar, true); @endphp
                                @foreach($fotos as $path)
                                <div class="col-4">
                                    <div class="rounded-4 overflow-hidden border shadow-sm">
                                        <img src="{{ asset('storage/' . $path) }}" class="img-premium img-lightbox w-100" style="aspect-ratio: 1/1; cursor: pointer; border: none; border-radius:0;">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    @php
                        // HANYA pelapor yang bisa melihat detail proses (stepper)
                        $isOwner = \Auth::check() && ($laporanKerusakan->id_user_pelapor == \Auth::id());
                    @endphp
                    <div class="col-md-5">
                        <div class="bg-light p-4 p-md-5 rounded-4 h-100 border">
                            @if($isOwner)
                                <h6 class="section-title border-bottom pb-3 mb-4">
                                    <i class="bi bi-graph-up-arrow me-2 font-primary"></i> Progress Perbaikan
                                </h6>

                                {{-- // stepper --}}
                                <div class="sc-stepper mb-5">
                                    <div class="sc-step completed">
                                        <div class="sc-step-icon"><i class="bi bi-check2"></i></div>
                                        <div class="sc-step-label">Dilaporkan</div>
                                    </div>
                                    <div class="sc-step {{ $isDiproses ? 'completed' : 'active' }}">
                                        <div class="sc-step-icon">@if($isDiproses)<i class="bi bi-check2"></i>@else 2 @endif</div>
                                        <div class="sc-step-label">Ditinjau</div>
                                    </div>
                                    <div class="sc-step {{ $isDalamKerja ? 'completed' : ($isDiproses ? 'active' : '') }}">
                                        <div class="sc-step-icon">@if($isDalamKerja)<i class="bi bi-check2"></i>@else 3 @endif</div>
                                        <div class="sc-step-label">Perbaikan</div>
                                    </div>
                                    <div class="sc-step {{ $isSelesai ? 'completed' : ($isDalamKerja ? 'active' : '') }}">
                                        <div class="sc-step-icon">@if($isSelesai)<i class="bi bi-check2"></i>@else 4 @endif</div>
                                        <div class="sc-step-label">Selesai</div>
                                    </div>
                                </div>

                                {{-- // info --}}
                                <div class="status-alert mb-4 shadow-sm border-0">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-info-circle-fill text-primary"></i>
                                        <span class="extra-small fw-900 text-primary text-uppercase" style="letter-spacing:0.5px;">Warta Status</span>
                                    </div>
                                    <p class="mb-0 small fw-bold text-muted" style="line-height: 1.5;">
                                        @if($status == 'dilaporkan')
                                            Laporan telah diterima. Tim Sarana Prasarana akan melakukan inspeksi dalam waktu dekat.
                                        @elseif($status == 'selesai')
                                            Fasilitas telah selesai diperbaiki. Terima kasih telah membantu menjaga kenyamanan kampus.
                                        @else
                                            Tim teknis sedang melakukan pengerjaan perbaikan di lokasi yang Anda laporkan.
                                        @endif
                                    </p>
                                </div>

                                {{-- Completion Proof logic removed from here as it's moved below --}}
                            @else
                                {{-- // publik --}}
                                <h6 class="section-title border-bottom pb-3 mb-4">
                                    <i class="bi bi-patch-check-fill me-2 font-primary"></i> Status Perbaikan
                                </h6>
                                <div class="text-center py-5 bg-white rounded-4 border border-dashed">
                                    @php
                                        $label = match($status) {
                                            'dilaporkan' => 'DIANTRIKAN',
                                            'diproses', 'ditugaskan', 'dalam_pengerjaan' => 'DALAM PENGERJAAN',
                                            'selesai' => 'SELESAI / DIPERBAIKI',
                                            default => strtoupper($status)
                                        };
                                        $label_color = match($status) {
                                            'selesai' => '#059669',
                                            'dilaporkan' => '#64748b',
                                            default => '#0A4174'
                                        };
                                        $label_bg = match($status) {
                                            'selesai' => '#ecfdf5',
                                            'dilaporkan' => '#f1f5f9',
                                            default => '#eff6ff'
                                        };
                                    @endphp
                                    <div class="badge rounded-pill px-4 py-3 mb-3 shadow-sm border" style="font-size: 0.85rem; letter-spacing: 1px; color: {{ $label_color }}; background-color: {{ $label_bg }}; border-color: {{ $label_color }}33 !important;">
                                        {{ $label }}
                                    </div>
                                    <p class="small fw-bold text-muted px-4 mb-0 opacity-75">Fasilitas ini telah selesai diperbaiki oleh pihak admin kampus.</p>
                                </div>
                            @endif

                            {{-- // bukti --}}
                            @if($isSelesai && ($laporanKerusakan->catatan_petugas || $laporanKerusakan->foto_bukti_pengerjaan))
                                <div class="mt-4 pt-4 border-top">
                                    @if($laporanKerusakan->catatan_petugas)
                                        <h6 class="section-title mb-3" style="font-size: 0.75rem;"><i class="bi bi-journal-check text-primary"></i> Catatan Hasil Perbaikan:</h6>
                                        <div class="p-3 bg-white rounded-4 small fw-bold text-dark italic border shadow-sm mb-4">
                                            "{{ $laporanKerusakan->catatan_petugas }}"
                                        </div>
                                    @endif

                                    @if($laporanKerusakan->foto_bukti_pengerjaan)
                                        <h6 class="section-title mb-3" style="font-size: 0.75rem;"><i class="bi bi-camera-fill text-primary"></i> Foto Bukti Hasil Perbaikan:</h6>
                                        <div class="row g-2">
                                            @php 
                                                $buktiFotos = is_array($laporanKerusakan->foto_bukti_pengerjaan) 
                                                    ? $laporanKerusakan->foto_bukti_pengerjaan 
                                                    : json_decode($laporanKerusakan->foto_bukti_pengerjaan, true); 
                                            @endphp
                                            @if($buktiFotos)
                                                @foreach($buktiFotos as $p)
                                                    <div class="col-4">
                                                        <div class="rounded-4 overflow-hidden border shadow-sm">
                                                            <img src="{{ asset('storage/' . $p) }}" class="img-premium img-lightbox w-100" style="aspect-ratio: 1/1; cursor: pointer; border-radius: 0;">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light p-4 text-center border-top">
                <small class="text-muted fw-bold text-uppercase letter-spacing-1">Sistem Lost & Found PNP &bull; Bersama Membangun Kampus Pintar</small>
            </div>
        </div>
    </div>
</div>
@endsection