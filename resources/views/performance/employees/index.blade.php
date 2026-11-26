@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('heading', 'Data Pegawai')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Data pegawai</h1>
            <p class="text-muted mb-0">Kelola pegawai yang akan masuk dalam proses penilaian.</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1" aria-hidden="true"></i> Tambah pegawai</a>
    </div>

    <section class="app-panel p-3 p-md-4 mb-3" aria-label="Cari pegawai">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label for="employee-search" class="form-label">Nama atau jabatan</label>
                <input id="employee-search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari pegawai">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Cari pegawai</button>
                @if (request()->has('search'))
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    <section class="app-panel" aria-label="Daftar pegawai">
        <div class="table-responsive">
            <table class="table app-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3 ps-md-4">Nama</th>
                        <th>Jabatan</th>
                        <th>No. Handphone</th>
                        <th>Email</th>
                        <th class="no-print text-end pe-3 pe-md-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td class="ps-3 ps-md-4 fw-semibold">{{ $employee->nama }}</td>
                            <td>{{ $employee->jabatan }}</td>
                            <td>{{ $employee->no_hp ?: 'Belum diisi' }}</td>
                            <td>{{ $employee->email ?: 'Belum diisi' }}</td>
                            <td class="text-nowrap no-print text-end pe-3 pe-md-4">
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                {{ request()->has('search') ? 'Tidak ada pegawai yang cocok dengan pencarian.' : 'Belum ada data pegawai. Tambahkan pegawai untuk mulai melakukan penilaian.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
