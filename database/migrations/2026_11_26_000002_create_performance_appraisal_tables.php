<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('kategori_kinerja', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 40)->unique();
            $table->string('nama', 120);
            $table->unsignedTinyInteger('bobot');
            $table->text('indikator');
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
        });

        Schema::create('penilaian_kinerja', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->char('periode', 7);
            $table->string('penilai', 50);
            $table->text('catatan')->nullable();
            $table->decimal('total', 5, 2);
            $table->timestamps();
            $table->unique(['pegawai_id', 'periode']);
        });

        Schema::create('detail_penilaian_kinerja', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian_kinerja')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_kinerja')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai');
            $table->timestamps();
            $table->unique(['penilaian_id', 'kategori_id']);
        });

        $now = now();
        DB::table('kategori_kinerja')->insert([
            [
                'kode' => 'kualitas',
                'nama' => 'Kualitas Hasil Kerja',
                'bobot' => 25,
                'indikator' => 'Akurasi, kelengkapan, dan minim kesalahan.',
                'urutan' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'produktivitas',
                'nama' => 'Produktivitas',
                'bobot' => 20,
                'indikator' => 'Pencapaian target dan volume kerja.',
                'urutan' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'disiplin_sop',
                'nama' => 'Disiplin & Kepatuhan SOP',
                'bobot' => 20,
                'indikator' => 'Kehadiran, disiplin, dan kepatuhan terhadap prosedur kerja.',
                'urutan' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ketepatan_waktu',
                'nama' => 'Ketepatan Waktu',
                'bobot' => 15,
                'indikator' => 'Konsistensi menyelesaikan tugas sesuai tenggat.',
                'urutan' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'kerja_sama',
                'nama' => 'Kerja Sama & Komunikasi',
                'bobot' => 10,
                'indikator' => 'Kolaborasi, komunikasi, dan dukungan kepada rekan kerja.',
                'urutan' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'tanggung_jawab',
                'nama' => 'Tanggung Jawab & Inisiatif',
                'bobot' => 10,
                'indikator' => 'Kemandirian, inisiatif, dan kemampuan menyelesaikan masalah.',
                'urutan' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penilaian_kinerja');
        Schema::dropIfExists('penilaian_kinerja');
        Schema::dropIfExists('kategori_kinerja');
        Schema::dropIfExists('pegawai');
    }
};
