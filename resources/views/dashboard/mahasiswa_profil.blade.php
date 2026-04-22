@extends('layouts.front')
@section('title', 'Profil Mahasiswa')

@section('content')
<div class="py-5" style="background-color: #f8fafc; min-height: 80vh;">
    <div class="container">
        {{-- Profile Banner --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div style="height: 120px; background: linear-gradient(135deg, #001D39 0%, #0A4174 100%);"></div>
                <div class="px-4 pb-4">
                    <div class="d-flex flex-column flex-md-row align-items-end gap-3" style="margin-top: -50px;">
                        <div class="bg-white p-1 rounded-circle shadow-sm">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 100px; height: 100px; font-size: 2.5rem; border: 4px solid white;">
                                {{ strtoupper(substr(\Auth::user()->mahasiswa->nama ?? \Auth::user()->email, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 mb-2">
                            <h2 class="fw-900 mb-0 text-dark">{{ \Auth::user()->mahasiswa->nama ?? 'Pengguna Smart Campus' }}</h2>
                            <p class="text-muted small fw-bold mb-0">
                                <i class="bi bi-person-badge me-1"></i> {{ \Auth::user()->mahasiswa->nim ?? 'NIM Tidak Terdaftar' }} 
                                <span class="mx-2 text-opacity-25 opacity-25">|</span>
                                <i class="bi bi-envelope me-1"></i> {{ \Auth::user()->email }}
                            </p>
                        </div>
                        <div class="mb-2">
                            <div class="bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-4 border border-warning border-opacity-25 d-flex align-items-center">
                                <i class="bi bi-star-fill me-2 fs-5"></i>
                                <div class="text-start">
                                    <div class="extra-small fw-800 text-uppercase" style="font-size: 0.6rem;">Reputasi</div>
                                    <div class="fw-900 fs-5 line-height-1">{{ \Auth::user()->poin }} <span class="small opacity-75">PTS</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Sidebar Info --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-800 text-uppercase small text-muted mb-0">Ringkasan Aktivitas</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <div class="fw-900 fs-4">{{ $stats['kehilangan'] }}</div>
                                    <div class="extra-small fw-bold text-muted text-uppercase">Kehilangan</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <div class="fw-900 fs-4">{{ $stats['temuan'] }}</div>
                                    <div class="extra-small fw-bold text-muted text-uppercase">Temuan</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <div class="fw-900 fs-4">{{ $stats['kerusakan'] }}</div>
                                    <div class="extra-small fw-bold text-muted text-uppercase">Kerusakan</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-4 text-center">
                                    <div class="fw-900 fs-4">{{ $stats['klaim'] }}</div>
                                    <div class="extra-small fw-bold text-muted text-uppercase">Klaim</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reputation History --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-800 text-uppercase small text-muted mb-0">Riwayat Poin</h6>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($riwayatPoin) && count($riwayatPoin) > 0)
                            <div class="vstack gap-3">
                                @foreach($riwayatPoin->take(5) as $entry)
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle {{ $entry['type'] == 'reward' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                            <i class="bi {{ $entry['type'] == 'reward' ? 'bi-plus-lg text-success' : 'bi-dash-lg text-danger' }} small"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="small fw-bold text-dark text-truncate">{{ $entry['description'] }}</div>
                                            <div class="extra-small text-muted">{{ $entry['date']->format('d M Y') }}</div>
                                        </div>
                                        <div class="fw-900 {{ $entry['type'] == 'reward' ? 'text-success' : 'text-danger' }}">+{{ $entry['poin'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <img src="https://illustrations.popsy.co/gray/not-found.svg" style="width: 80px;" class="mb-2">
                                <p class="small text-muted fw-bold mb-0">Belum ada riwayat poin.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Papan Peringkat (Leaderboard) --}}
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-800 text-uppercase small text-muted mb-0">Papan Peringkat</h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary extra-small fw-800">TOP 5</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="vstack gap-3">
                            @forelse($leaderboard as $rank => $topUser)
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center fw-900 rounded-circle 
                                        {{ $rank == 0 ? 'bg-warning text-dark' : ($rank == 1 ? 'bg-secondary text-white' : ($rank == 2 ? 'bg-danger text-white bg-opacity-75' : 'bg-light text-muted')) }}" 
                                        style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ $rank + 1 }}
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="small fw-bold text-dark text-truncate">{{ $topUser->mahasiswa->nama ?? explode('@', $topUser->email)[0] }}</div>
                                        <div class="extra-small text-muted">{{ $topUser->mahasiswa->nim ?? 'MAHASISWA' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-900 text-primary small">{{ $topUser->poin }}</div>
                                        <div class="extra-small text-muted" style="font-size: 0.6rem;">PTS</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <p class="small text-muted mb-0">Belum ada peringkat.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-0">
                        <ul class="nav nav-pills p-2 gap-2" id="reportTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-3 small fw-800 py-2 px-4" id="temuan-tab" data-bs-toggle="tab" data-bs-target="#temuan-content" type="button" role="tab">TEMUAN SAYA</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-3 small fw-800 py-2 px-4" id="kehilangan-tab" data-bs-toggle="tab" data-bs-target="#kehilangan-content" type="button" role="tab">KEHILANGAN</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-3 small fw-800 py-2 px-4" id="klaim-tab" data-bs-toggle="tab" data-bs-target="#klaim-content" type="button" role="tab">KLAIM SAYA</button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="reportTabsContent">
                        {{-- Tab Temuan --}}
                        <div class="tab-pane fade show active" id="temuan-content" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="extra-small text-muted fw-800 text-uppercase border-0">
                                            <th class="px-4 py-3">Nama Barang</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3 text-end px-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTemuan as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                                    <div class="extra-small text-muted">{{ $item->created_at->format('d M Y') }}</div>
                                                </td>
                                                <td class="py-3">
                                                    @if($item->status_penyerahan == 'sudah_diterima')
                                                        <span class="badge bg-success rounded-pill px-3 py-2 small fw-bold">Diterima Admin</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 small fw-bold">Belum Diserahkan</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 text-end px-4">
                                                    <a href="{{ route('mahasiswa.temuan.show', $item->id_barang) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Belum ada laporan temuan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab Kehilangan --}}
                        <div class="tab-pane fade" id="kehilangan-content" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="extra-small text-muted fw-800 text-uppercase border-0">
                                            <th class="px-4 py-3">Barang Hilang</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3 text-end px-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentKehilangan as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                                    <div class="extra-small text-muted">{{ $item->lokasi_hilang }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-info text-dark rounded-pill px-3 py-2 small fw-bold text-uppercase">{{ $item->status }}</span>
                                                </td>
                                                <td class="py-3 text-end px-4">
                                                    <a href="{{ route('mahasiswa.kehilangan.show', $item->id_laporan) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Belum ada laporan kehilangan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab Klaim --}}
                        <div class="tab-pane fade" id="klaim-content" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="extra-small text-muted fw-800 text-uppercase border-0">
                                            <th class="px-4 py-3">Barang Diklaim</th>
                                            <th class="py-3">Status Klaim</th>
                                            <th class="py-3 text-end px-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($klaimSaya as $claim)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="fw-bold text-dark">{{ $claim->barangTemuan->nama_barang }}</div>
                                                </td>
                                                <td class="py-3">
                                                    @php $c = match($claim->status_klaim) { 'disetujui' => 'success', 'ditolak' => 'danger', default => 'warning' }; @endphp
                                                    <span class="badge bg-{{ $c }} rounded-pill px-3 py-2 small fw-bold text-uppercase">{{ $claim->status_klaim }}</span>
                                                </td>
                                                <td class="py-3 text-end px-4">
                                                    <a href="{{ route('mahasiswa.klaim.show', $claim->id_klaim) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Belum ada klaim barang.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Link Section --}}
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                            <i class="bi bi-box-seam fs-1 text-primary mb-3"></i>
                            <h6 class="fw-bold mb-2">Temukan Barang?</h6>
                            <p class="small text-muted mb-3">Bantu sesama warga kampus dengan melaporkan barang temuan Anda.</p>
                            <a href="{{ route('mahasiswa.temuan.create') }}" class="btn btn-primary rounded-pill fw-bold w-100 mt-auto">LAPOR TEMUAN</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                            <i class="bi bi-search fs-1 text-danger mb-3"></i>
                            <h6 class="fw-bold mb-2">Kehilangan Barang?</h6>
                            <p class="small text-muted mb-3">Catat laporan kehilangan Anda agar tim Admin dapat membantu mencari.</p>
                            <a href="{{ route('mahasiswa.kehilangan.create') }}" class="btn btn-danger rounded-pill fw-bold w-100 mt-auto">LAPOR KEHILANGAN</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

