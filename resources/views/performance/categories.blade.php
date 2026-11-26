@extends('layouts.app')

@section('title', 'Kategori Kinerja')
@section('heading', 'Kategori Kinerja')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Kategori penilaian kinerja</h1>
        <p class="text-muted mb-0">Bobot setiap kategori digunakan untuk menghitung nilai akhir.</p>
    </div>
    <div class="app-panel">
        <div class="table-responsive">
            <table class="table app-table mb-0 align-middle">
                <thead>
                    <tr><th>No</th><th>Kategori</th><th>Bobot</th><th>Indikator</th></tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->nama }}</td>
                            <td>{{ $category->bobot }}%</td>
                            <td>{{ $category->indikator }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold"><td colspan="2" class="text-end">Total Bobot</td><td>100%</td><td></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="app-panel p-3 mt-3">
        Skor tiap kategori memakai skala 1–4. Nilai akhir adalah rata-rata berbobot dengan rentang 1–4.
    </div>
@endsection
