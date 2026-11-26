@extends('layouts.app')

@section('title', 'Buat Penilaian Kinerja')
@section('heading', 'Buat Penilaian Kinerja')

@section('content')
    @if ($employees->isEmpty())
        <div class="alert alert-warning">
            Tambahkan data pegawai terlebih dahulu sebelum membuat penilaian.
            <a href="{{ route('employees.create') }}" class="alert-link">Tambah pegawai</a>
        </div>
    @else
        <div class="app-panel mb-3">
            <div class="card-body">
                <h1 class="h4 mb-3">Penilaian Kinerja Pegawai</h1>
                <p class="text-muted mb-0">Beri skor 1–4 berdasarkan indikator pada setiap kategori. Pengisian ulang untuk pegawai dan periode yang sama akan memperbarui penilaian sebelumnya.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('performance.store') }}">
            @csrf
            <div class="app-panel mb-3">
                <div class="p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pegawai_id" class="form-label">Pegawai</label>
                            <select id="pegawai_id" name="pegawai_id" class="form-select @error('pegawai_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected((string) old('pegawai_id') === (string) $employee->id)>{{ $employee->nama }} ({{ $employee->jabatan }})</option>
                                @endforeach
                            </select>
                            @error('pegawai_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="periode" class="form-label">Periode Penilaian</label>
                            <input type="month" id="periode" name="periode" value="{{ old('periode', now()->format('Y-m')) }}" class="form-control @error('periode') is-invalid @enderror" required>
                            @error('periode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-panel">
                <div class="table-responsive">
                    <table class="table app-table mb-0 align-middle">
                        <thead>
                            <tr><th>Kategori & Indikator</th><th>Bobot</th><th>Skor</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->nama }}</strong>
                                        <div class="small text-muted">{{ $category->indikator }}</div>
                                    </td>
                                    <td>{{ $category->bobot }}%</td>
                                    <td style="min-width: 220px">
                                        <label for="score-{{ $category->id }}" class="form-label small mb-1">Nilai (1–4)</label>
                                        <select id="score-{{ $category->id }}" name="nilai[{{ $category->id }}]" class="form-select @error('nilai.'.$category->id) is-invalid @enderror" required>
                                            <option value="">-- Pilih skor --</option>
                                            @foreach ([1 => 'Kurang', 2 => 'Cukup', 3 => 'Baik', 4 => 'Sangat Baik'] as $score => $label)
                                                <option value="{{ $score }}" @selected((string) old('nilai.'.$category->id) === (string) $score)>{{ $score }}: {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('nilai.'.$category->id) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 p-md-4">
                    <label for="catatan" class="form-label">Catatan (opsional)</label>
                    <textarea id="catatan" name="catatan" rows="3" maxlength="2000" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan Penilaian</button>
            <a href="{{ route('performance.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    @endif
@endsection
