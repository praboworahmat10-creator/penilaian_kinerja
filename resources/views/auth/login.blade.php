<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Penilaian Kinerja Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #faeaea; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .left-panel {
            background-color: #b71217; color: white; display: flex; flex-direction: column;
            justify-content: center; align-items: center; padding: 2rem; min-height: 100vh;
        }
        .logo {
            width: 100px; height: 100px; background: white url('{{ asset('images/logo.png') }}') center / contain no-repeat;
            border-radius: 50%; margin-bottom: 1rem;
        }
        .login-card { border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,.1); padding: 2rem; }
        .btn-primary { background-color: #b71217; border: 0; }
        .btn-primary:hover { background-color: #960f13; }
        .form-control:focus { border-color: #b71217; box-shadow: 0 0 0 .25rem rgba(183,18,23,.25); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-6 left-panel text-center">
            <div class="logo"></div>
            <h6 class="mt-3">PT. Meissa Berkah Teknologi</h6>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
            <div class="login-card bg-white w-100" style="max-width: 420px;">
                <h4 class="fw-bold text-center">Selamat Datang</h4>
                <p class="text-center text-muted mb-4" style="font-size: .9rem;">Masukkan username dan password untuk mengakses aplikasi.</p>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" value="{{ old('username') }}" placeholder="Tulis nama pengguna" required autofocus>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan kata sandi" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password" aria-label="Tampilkan kata sandi">
                                <i class="bi bi-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const password = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !visible);
        icon.classList.toggle('bi-eye-slash', visible);
    });
</script>
</body>
</html>
