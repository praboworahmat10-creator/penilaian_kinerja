@extends('layouts.app')

@section('title', 'Ringkasan Kinerja')
@section('heading', 'Ringkasan')

@section('content')
    <section class="dashboard-intro mb-4">
        <div>
            <div class="period-label">Periode berjalan · {{ $periodLabel }}</div>
            <h1>Selamat datang, {{ session('username') }}</h1>
            <p>Berikut ringkasan data penilaian kinerja pegawai untuk membantu memantau progres periode ini.</p>
        </div>
    </section>

    <h2 class="h5 mb-3">Statistik periode {{ $periodLabel }}</h2>
    <section class="row g-3 mb-4" aria-label="Ringkasan periode {{ $periodLabel }}">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Total pegawai</div>
                <div class="stat-value">{{ $totalEmployees }}</div>
                <div class="stat-note">Terdaftar dalam sistem</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Penilaian periode ini</div>
                <div class="stat-value">{{ $currentPeriodCount }}</div>
                <div class="stat-note">Periode {{ $period }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Rata-rata nilai</div>
                <div class="stat-value">{{ $averageScore === null ? 'Belum ada' : number_format($averageScore, 2) }}</div>
                <div class="stat-note">Skala penilaian 1 sampai 4</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Belum dinilai</div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-note">Pegawai tanpa penilaian periode ini</div>
            </div>
        </div>
    </section>

    <div class="row g-3 align-items-start">
        <section class="col-xl-8" aria-labelledby="latest-assessments-title">
            <div class="app-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 p-md-4 border-bottom" style="border-color: var(--line) !important">
                    <div>
                        <h2 id="latest-assessments-title" class="h5 mb-1">Data penilaian terbaru</h2>
                        <p class="text-muted small mb-0">Lima catatan terakhir yang diperbarui</p>
                    </div>
                    <a href="{{ route('performance.index') }}" class="btn btn-sm btn-outline-secondary">Semua penilaian</a>
                </div>
                @if ($recentAssessments->isEmpty())
                    <div class="empty-state">
                        <p class="mb-2">Belum ada penilaian yang tercatat.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table app-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3 ps-md-4">Pegawai</th>
                                    <th>Periode</th>
                                    <th>Nilai</th>
                                    <th>Predikat</th>
                                    <th class="text-end pe-3 pe-md-4">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAssessments as $assessment)
                                    <tr>
                                        <td class="ps-3 ps-md-4">
                                            <strong>{{ $assessment->nama }}</strong>
                                            <div class="small text-muted">{{ $assessment->jabatan }}</div>
                                        </td>
                                        <td>{{ $assessment->periode }}</td>
                                        <td class="fw-semibold">{{ number_format((float) $assessment->total, 2) }}</td>
                                        <td>{{ $assessment->predikat }}</td>
                                        <td class="text-end pe-3 pe-md-4">
                                            <a href="{{ route('performance.show', $assessment->id) }}" class="btn btn-sm btn-outline-secondary" aria-label="Lihat penilaian {{ $assessment->nama }}">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <aside class="col-xl-4" aria-labelledby="follow-up-title">
            <div class="app-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h2 id="follow-up-title" class="h5 mb-2">Cakupan penilaian</h2>
                        <p class="small text-muted mb-0">Pegawai yang belum memiliki penilaian untuk {{ $periodLabel }}.</p>
                    </div>
                    <i class="bi bi-clipboard-check fs-4" style="color: var(--teal)" aria-hidden="true"></i>
                </div>
                <div class="display-5 fw-semibold mt-4">{{ $pendingCount }}</div>
                <p class="text-muted small">dari {{ $totalEmployees }} pegawai terdaftar</p>
                @if ($totalEmployees > 0)
                    <a href="{{ route('performance.create') }}" class="btn btn-primary w-100">Buat penilaian periode ini</a>
                @else
                    <a href="{{ route('employees.create') }}" class="btn btn-primary w-100">Tambahkan pegawai</a>
                @endif
            </div>
        </aside>
    </div>
@endsection
