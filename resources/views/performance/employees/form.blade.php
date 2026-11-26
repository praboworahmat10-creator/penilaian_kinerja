@extends('layouts.app')

@php
    $editing = isset($employee);
@endphp

@section('title', $editing ? 'Edit Pegawai' : 'Tambah Pegawai')
@section('heading', $editing ? 'Edit Pegawai' : 'Tambah Pegawai')

@section('content')
    <div class="app-panel">
        <div class="p-3 p-md-4">
            <h1 class="h4 mb-4">{{ $editing ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h1>
            <form method="POST" action="{{ $editing ? route('employees.update', $employee->id) : route('employees.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input id="nama" name="nama" value="{{ old('nama', $employee->nama ?? '') }}" class="form-control @error('nama') is-invalid @enderror" required maxlength="100">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input id="jabatan" name="jabatan" value="{{ old('jabatan', $employee->jabatan ?? '') }}" class="form-control @error('jabatan') is-invalid @enderror" required maxlength="100">
                    @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No. Handphone (opsional)</label>
                    <input id="no_hp" name="no_hp" value="{{ old('no_hp', $employee->no_hp ?? '') }}" class="form-control @error('no_hp') is-invalid @enderror" maxlength="20">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (opsional)</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" maxlength="100">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Simpan' }}</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
