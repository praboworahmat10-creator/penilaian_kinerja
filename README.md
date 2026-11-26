# Sistem Penilaian Kinerja Pegawai

Aplikasi Laravel untuk mengelola data pegawai dan penilaian kinerja berdasarkan periode. Fitur rekrutmen tidak lagi tersedia di aplikasi. Tabel dan data rekrutmen lama di database tetap dipertahankan sebagai arsip; data pelamar tidak otomatis disalin menjadi pegawai.

## Menjalankan aplikasi

1. Pastikan MySQL aktif dan database `kkp_db` tersedia.
2. Impor `kkp_db.sql` dari folder induk ke database tersebut. Dump ini menyediakan tabel akun lama yang digunakan untuk login serta mempertahankan data arsip.
3. Atur kredensial database di `.env`.
4. Dari folder proyek Laravel, jalankan:

   ```bash
   php artisan migrate
   php artisan serve --host 127.0.0.1 --port 8002
   ```

5. Buka `http://127.0.0.1:8002`.

Login menggunakan tabel `users` lama. Hash MD5 akan diperbarui ke bcrypt setelah login berhasil.

## Fitur

- Dashboard ringkasan dengan total pegawai, penilaian periode aktif, nilai rata-rata, dan pegawai yang belum dinilai.
- Tema terang/gelap yang dapat diganti dan tersimpan di browser.
- Pencarian pegawai berdasarkan nama/jabatan dan filter riwayat penilaian berdasarkan nama, jabatan, atau periode.
- Cetak laporan penilaian per pegawai.
- Data pegawai: tambah, ubah, dan hapus (pegawai dengan riwayat penilaian tidak dapat dihapus).
- Enam kategori penilaian dengan bobot total 100%.
- Penilaian per periode dengan skor 1–4, nilai akhir berbobot, dan predikat.
- Menyimpan ulang penilaian pegawai pada periode yang sama akan memperbarui penilaian tersebut.

**Arah tampilan:** profesional korporat, ENERGY 2 / RHYTHM 2 / MOTION 1. Navy menegaskan navigasi, aksen hijau-teal menandai tindakan utama, dan permukaan terang menjaga tabel mudah dibaca. Komposisi memprioritaskan status periode dan catatan terbaru karena itulah informasi utama HRD saat membuka aplikasi. Tipografi sistem menjaga tampilan familiar pada perangkat kantor. Tema gelap tersedia untuk kenyamanan pengguna.

Kategori bawaan:

| Kategori | Bobot |
|---|---:|
| Kualitas Hasil Kerja | 25% |
| Produktivitas | 20% |
| Disiplin & Kepatuhan SOP | 20% |
| Ketepatan Waktu | 15% |
| Kerja Sama & Komunikasi | 10% |
| Tanggung Jawab & Inisiatif | 10% |

## Pengujian

```bash
php artisan test
```
