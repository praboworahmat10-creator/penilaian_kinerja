@extends('layouts.app')

@section('title', 'Penilaian Kinerja')
@section('heading', 'Penilaian Kinerja')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Riwayat penilaian</h1>
            <p class="text-muted mb-0">Cari pegawai atau saring catatan berdasarkan periode.</p>
        </div>
        <a href="{{ route('performance.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Buat penilaian</a>
    </div>

    <section class="app-panel p-3 p-md-4 mb-3" aria-label="Filter riwayat penilaian">
        <form method="GET" action="{{ route('performance.index') }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="assessment-search" class="form-label">Nama pegawai atau jabatan</label>
                <input id="assessment-search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Contoh: Analis">
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="assessment-period" class="form-label">Periode</label>
                <input type="month" id="assessment-period" name="periode" value="{{ request('periode') }}" class="form-control">
            </div>
            <div class="col-sm-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                @if (request()->hasAny(['search', 'periode']))
                    <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    <section class="app-panel" aria-label="Daftar penilaian">
        <div class="table-responsive">
            <table class="table app-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3 ps-md-4">Periode</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Nilai</th>
                        <th>Predikat</th>
                        <th>Penilai</th>
                        <th class="no-print text-end pe-3 pe-md-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                        <tr>
                            <td class="ps-3 ps-md-4">{{ $assessment->periode }}</td>
                            <td class="fw-semibold">{{ $assessment->nama }}</td>
                            <td>{{ $assessment->jabatan }}</td>
                            <td>{{ number_format((float) $assessment->total, 2) }} / 4</td>
                            <td>{{ $assessment->predikat }}</td>
                            <td>{{ $assessment->penilai }}</td>
                            <td class="text-nowrap no-print text-end pe-3 pe-md-4">
                                <a href="{{ route('performance.show', $assessment->id) }}" class="btn btn-outline-secondary btn-sm">Detail</a>
                                <form method="POST" action="{{ route('performance.destroy', $assessment->id) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus penilaian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                {{ request()->hasAny(['search', 'periode']) ? 'Tidak ada penilaian yang cocok dengan filter.' : 'Belum ada penilaian kinerja.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
