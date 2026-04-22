@extends(\Auth::user()->role === 'admin' ? 'layouts.app' : 'layouts.front')
@section('title', 'Verifikasi Klaim - ' . $klaimBarang->nama_pemilik)

@push('styles')
<style>
    .page-header-simple {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-status {
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .score-banner {
        border-radius: 12px;
        padding: 16px 24px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-left: 6px solid;
    }

    .score-banner.high { background: #f0fdf4; border-color: #16a34a; color: #166534; }
    .score-banner.medium { background: #fffbeb; border-color: #ca8a04; color: #854d0e; }
    .score-banner.low { background: #fef2f2; border-color: #dc2626; color: #991b1b; }

    .card-simple {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        overflow: hidden;
    }

    .label-section {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        display: block;
    }

    .info-list { width: 100%; }
    .info-list tr { border-bottom: 1px solid #f1f5f9; }
    .info-list tr:last-child { border-bottom: none; }
    .info-list th { padding: 14px 0; font-weight: 600; color: #64748b; font-size: 0.9rem; width: 45%; }
    .info-list td { padding: 14px 0; font-weight: 700; color: #1e293b; font-size: 0.9rem; text-align: right; }

    .ref-item-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px;
        border: 1px solid #e2e8f0;
    }

    .comparison-table {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .comparison-table th {
        background: #001D39;
        color: white;
        padding: 15px 20px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .comparison-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .comparison-label {
        font-weight: 800;
        color: #64748b;
        background: #f8fafc;
        width: 25%;
    }
    .comparison-data {
        width: 37.5%;
        font-weight: 700;
    }
    .match-highlight {
        background-color: #f0fdf4 !important;
        color: #166534 !important;
    }

</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('petugas.klaim.index') }}" class="btn btn-outline-secondary border rounded-pill px-4 btn-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean
    </a>
</div>

<div class="page-header-simple shadow-sm">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h4 class="fw-bold mb-0 text-dark">Detail Verifikasi Klaim</h4>
            <span class="badge bg-light text-muted border">ID: #AC-{{ $klaimBarang->id_klaim }}</span>
        </div>
        <div class="text-muted small">Verifikasi kepemilikan barang untuk pengajuan ini.</div>
    </div>
    @php 
        $s = match($klaimBarang->status_klaim) { 
            'disetujui' => ['bg' => '#ecfdf5', 'color' => '#059669', 'label' => 'Sudah Disetujui'], 
            'ditolak' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Ditolak'], 
            default => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'Menunggu Verifikasi'] 
        }; 
    @endphp
    <div class="badge-status shadow-sm" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }};">
        {{ $s['label'] }}
    </div>
</div>

@if($klaimBarang->skor_kecocokan !== null)
    @php
        $type = $klaimBarang->skor_kecocokan >= 70 ? 'high' : ($klaimBarang->skor_kecocokan >= 40 ? 'medium' : 'low');
        $icon = $klaimBarang->skor_kecocokan >= 70 ? 'bi-check-all' : ($klaimBarang->skor_kecocokan >= 40 ? 'bi-exclamation-triangle' : 'bi-shield-x');
    @endphp
    <div class="score-banner {{ $type }} shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-2 fw-bold">{{ $klaimBarang->skor_kecocokan }}%</div>
            <div>
                <div class="fw-bold fs-6">Analisis Kecocokan Sistem</div>
                <div class="small opacity-85">
                    @if($type == 'high') Hasil analisis tinggi. Sangat mungkin ini adalah pemilik asli barang tersebut.
                    @elseif($type == 'medium') Hasil analisis sedang. Mohon verifikasi bukti fisik dengan teliti.
                    @else Hasil analisis rendah. Terdapat banyak perbedaan data antara klaim dan data barang.
                    @endif
                </div>
            </div>
        </div>
        <i class="bi {{ $icon }} display-6 opacity-50"></i>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-12">
        <div class="comparison-table shadow-sm">
            <table class="table mb-0 w-100">
                <thead>
                    <tr>
                        <th class="comparison-label">Kategori Data</th>
                        <th>Data Klaim (Pemilik)</th>
                        <th>Data Temuan (Penemu)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="comparison-label small">Nama Barang</td>
                        <td class="comparison-data">{{ $klaimBarang->barangTemuan->nama_barang }}</td>
                        <td class="comparison-data text-muted">{{ $klaimBarang->barangTemuan->nama_barang }}</td>
                    </tr>
                    @php
                        $warnaMatch = !empty($klaimBarang->warna) && !empty($klaimBarang->barangTemuan->warna) && strtolower($klaimBarang->warna) == strtolower($klaimBarang->barangTemuan->warna);
                        $merkMatch = !empty($klaimBarang->merk) && !empty($klaimBarang->barangTemuan->merk) && strtolower($klaimBarang->merk) == strtolower($klaimBarang->barangTemuan->merk);
                    @endphp
                    <tr>
                        <td class="comparison-label small">Warna</td>
                        <td class="comparison-data {{ $warnaMatch ? 'match-highlight' : '' }} fw-bold">{{ $klaimBarang->warna ?: '-' }}</td>
                        <td class="comparison-data {{ $warnaMatch ? 'match-highlight' : '' }}">{{ $klaimBarang->barangTemuan->warna ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="comparison-label small">Merk / Brand</td>
                        <td class="comparison-data {{ $merkMatch ? 'match-highlight' : '' }} fw-bold">{{ $klaimBarang->merk ?: '-' }}</td>
                        <td class="comparison-data {{ $merkMatch ? 'match-highlight' : '' }}">{{ $klaimBarang->barangTemuan->merk ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="comparison-label small">Lokasi</td>
                        <td class="comparison-data text-danger fw-bold"><i class="bi bi-geo-alt me-1"></i> {{ $klaimBarang->lokasi_hilang }}</td>
                        <td class="comparison-data text-primary fw-bold"><i class="bi bi-geo-alt me-1"></i> {{ $klaimBarang->barangTemuan->lokasi_nama }}</td>
                    </tr>
                    <tr>
                        <td class="comparison-label small">Tanggal</td>
                        <td class="comparison-data text-danger fw-bold"><i class="bi bi-calendar-x me-1"></i> {{ $klaimBarang->tanggal_hilang->format('d/m/Y') }} <div class="extra-small text-muted fw-normal">Dilaporkan Hilang</div></td>
                        <td class="comparison-data text-primary fw-bold"><i class="bi bi-calendar-check me-1"></i> {{ $klaimBarang->barangTemuan->tanggal_ditemukan->format('d/m/Y') }} <div class="extra-small text-muted fw-normal">Ditemukan Oleh Penemu</div></td>
                    </tr>
                    <tr>
                        <td class="comparison-label small">Deskripsi / Ciri</td>
                        <td class="comparison-data small fw-medium" style="line-height: 1.5;">{{ $klaimBarang->deskripsi_ciri_khusus }}</td>
                        <td class="comparison-data small fw-medium text-muted" style="line-height: 1.5;">{{ $klaimBarang->barangTemuan->deskripsi_singkat }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-simple p-4 p-md-5 shadow-sm">
            <div class="mb-5">
                <span class="label-section">Data Pengaju Klaim</span>
                <table class="info-list">
                    <tr><th>Nama Lengkap</th><td>{{ $klaimBarang->nama_pemilik }}</td></tr>
                    <tr><th>Email Terdaftar</th><td>{{ $klaimBarang->pengaju->email }}</td></tr>
                    <tr><th>Waktu Pengajuan</th><td>{{ $klaimBarang->tanggal_pengajuan->format('d M Y, H:i') }}</td></tr>
                    <tr><th>Lokasi & Tgl Kehilangan</th><td>{{ $klaimBarang->lokasi_hilang }} ({{ $klaimBarang->tanggal_hilang->format('d/m/Y') }})</td></tr>
                </table>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <span class="label-section">Ciri-ciri Khusus</span>
                    <div class="p-3 bg-light rounded-4 border-0 text-dark small" style="min-height: 100px;">
                        {{ $klaimBarang->deskripsi_ciri_khusus }}
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="label-section">Isi / Kandungan Barang</span>
                    <div class="p-3 bg-light rounded-4 border-0 text-dark small" style="min-height: 100px;">
                        {{ $klaimBarang->isi_barang ?: 'Tidak disebutkan.' }}
                    </div>
                </div>
            </div>

            @if($klaimBarang->merk || $klaimBarang->tipe_model || $klaimBarang->warna || $klaimBarang->nomor_seri)
            <div class="mb-5">
                <span class="label-section">Spesifikasi Fisik</span>
                <table class="info-list">
                    @if($klaimBarang->merk)<tr><th>Merk</th><td>{{ $klaimBarang->merk }}</td></tr>@endif
                    @if($klaimBarang->tipe_model)<tr><th>Model / Tipe</th><td>{{ $klaimBarang->tipe_model }}</td></tr>@endif
                    @if($klaimBarang->warna)<tr><th>Warna</th><td>{{ $klaimBarang->warna }}</td></tr>@endif
                    @if($klaimBarang->nomor_seri)<tr><th>Nomor Seri</th><td>{{ $klaimBarang->nomor_seri }}</td></tr>@endif
                </table>
            </div>
            @endif

            @if($klaimBarang->foto_bukti)
            <div>
                <span class="label-section">Foto Bukti Kepemilikan</span>
                <div class="rounded-4 overflow-hidden border shadow-sm mt-3">
                    <img src="{{ asset('storage/' . $klaimBarang->foto_bukti) }}" class="w-100 object-fit-cover" style="max-height: 500px;">
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-simple p-3 shadow-sm mb-3">
            <span class="label-section mb-3">Keputusan Verifikasi</span>

            @if(\Auth::user()->role === 'admin')
                @if($klaimBarang->status_klaim != 'menunggu')
                    {{-- READ-ONLY: Klaim sudah diproses --}}
                    <div class="text-center py-3">
                        @if($klaimBarang->status_klaim == 'disetujui')
                            <h5 class="fw-bold text-success mb-1 small">Klaim Disetujui</h5>
                            <p class="text-muted extra-small mb-2">Barang telah diserahkan kepada pemiliknya.</p>
                        @else
                            <h5 class="fw-bold text-danger mb-1 small">Klaim Ditolak</h5>
                            <p class="text-muted extra-small mb-2">Klaim ini telah ditolak.</p>
                        @endif

                        @if($klaimBarang->catatan_admin)
                        <div class="p-3 bg-light rounded-4 border text-start mt-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size:0.65rem;">Catatan Admin</div>
                            <div class="small text-dark">{{ $klaimBarang->catatan_admin }}</div>
                        </div>
                        @endif

                        @if($klaimBarang->tanggal_verifikasi)
                        <div class="text-muted small mt-3">
                            <i class="bi bi-clock me-1"></i> Diverifikasi pada {{ $klaimBarang->tanggal_verifikasi->format('d M Y, H:i') }}
                        </div>
                        @endif
                    </div>
                @else
                    {{-- FORM AKTIF: Status masih menunggu --}}
                    <p class="text-muted small mb-4">Silakan tentukan hasil verifikasi klaim ini. Jika setuju, maka status barang temuan akan otomatis berubah menjadi 'Sudah Diambil'.</p>
                    
                    <form action="{{ route('petugas.klaim.update', $klaimBarang->id_klaim) }}" method="POST" id="form-update">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Tindakan Admin</label>
                            <select class="form-select form-select-sm border-2" name="status_klaim" required style="border-radius: 8px; font-weight: 600;">
                                <option value="menunggu" selected>PROSES VERIFIKASI</option>
                                <option value="disetujui">SETUJUI KLAIM</option>
                                <option value="ditolak">TOLAK KLAIM</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Catatan Verifikator</label>
                            <textarea class="form-control border-2 small" name="catatan_admin" rows="2" placeholder="Alasan verifikasi..." style="border-radius: 8px; font-size: 0.85rem;">{{ $klaimBarang->catatan_admin }}</textarea>
                        </div>
                        
                        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm small" 
                            onclick="confirmAction('form-update', 'Simpan Keputusan?', 'Status klaim akan diperbarui.')">
                            Simpan Keputusan
                        </button>
                    </form>
                @endif
            @else
                {{-- Read Only untuk Petugas --}}
                <div class="text-center py-4">
                    <i class="bi bi-shield-check text-muted display-4 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-bold text-dark">Akses Terbatas</h5>
                    <p class="text-muted small mb-0">Verifikasi klaim hanya dapat dilakukan oleh Admin sistem.</p>
                    @if($klaimBarang->status_klaim != 'menunggu')
                        <div class="mt-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }}; font-size: 0.75rem;">
                                {{ strtoupper($s['label']) }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="card-simple p-3 shadow-sm bg-light bg-opacity-50">
            <span class="label-section mb-3">Referensi Barang Asli</span>
            @if($klaimBarang->barangTemuan)
                @php
                    $fotosRef = is_array($klaimBarang->barangTemuan->foto) ? $klaimBarang->barangTemuan->foto : json_decode($klaimBarang->barangTemuan->foto, true);
                    $fotoRef = ($fotosRef && count($fotosRef) > 0) ? $fotosRef[0] : null;
                @endphp
                <div class="ref-item-card p-2">
                    @if($fotoRef)
                        <img src="{{ asset('storage/' . $fotoRef) }}" class="w-100 rounded-3 mb-2 shadow-sm" style="height: 100px; object-fit: cover;">
                    @endif
                    <h6 class="fw-bold mb-1 small">{{ $klaimBarang->barangTemuan->nama_barang }}</h6>
                    <span class="badge bg-white text-dark shadow-sm border mb-2" style="font-size: 0.6rem;">{{ $klaimBarang->barangTemuan->kategori->nama_kategori }}</span>
                    
                    <div class="extra-small mb-1 text-muted text-truncate"><strong>Lokasi:</strong> {{ $klaimBarang->barangTemuan->lokasi_nama }}</div>
                    <div class="text-muted opacity-75" style="font-size: 0.75rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $klaimBarang->barangTemuan->deskripsi_singkat }}</div>
                </div>
            @else
                <div class="alert alert-warning small mb-0">Data barang temuan asli sudah tidak tersedia.</div>
            @endif
        </div>
    </div>
</div>
@endsection
