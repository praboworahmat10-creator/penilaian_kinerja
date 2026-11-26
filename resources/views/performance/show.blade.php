@extends('layouts.app')

@section('title', 'Detail Penilaian Kinerja')
@section('heading', 'Detail Penilaian Kinerja')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $assessment->nama }}</h1>
            <div class="text-muted">{{ $assessment->jabatan }} · Periode {{ $assessment->periode }}</div>
        </div>
        <div class="d-flex gap-2 no-print">
            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1" aria-hidden="true"></i> Cetak laporan</button>
            <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="app-panel">
        <div class="table-responsive">
            <table class="table app-table app-detail-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th scope="col">Kategori</th>
                        <th scope="col">Indikator</th>
                        <th scope="col" class="text-center">Bobot</th>
                        <th scope="col" class="text-center">Skor</th>
                        <th scope="col" class="text-end">Kontribusi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td>{{ $detail->nama }}</td>
                            <td>{{ $detail->indikator }}</td>
                            <td class="text-center">{{ $detail->bobot }}%</td>
                            <td class="text-center">{{ $detail->nilai }}</td>
                            <td class="text-end">{{ number_format($detail->kontribusi, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold app-total-row">
                        <td colspan="4" class="text-end">Nilai Akhir (skala 1–4)</td>
                        <td class="text-end">{{ number_format((float) $assessment->total, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="assessment-summary">
            <dl class="assessment-meta mb-0">
                <div>
                    <dt>Predikat</dt>
                    <dd>{{ $assessment->predikat }}</dd>
                </div>
                <div>
                    <dt>Penilai</dt>
                    <dd>{{ $assessment->penilai }}</dd>
                </div>
                <div class="assessment-note">
                    <dt>Catatan</dt>
                    <dd>{{ $assessment->catatan ?: 'Tidak ada catatan.' }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
