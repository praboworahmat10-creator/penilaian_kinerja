<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerformanceOnlyScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('penilaian_kinerja');
        Schema::dropIfExists('pegawai');
        Schema::create('pegawai', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->timestamps();
        });

        Schema::create('penilaian_kinerja', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('pegawai_id');
            $table->char('periode', 7);
            $table->decimal('total', 5, 2);
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function test_dashboard_only_exposes_performance_appraisal_features(): void
    {
        $employeeId = DB::table('pegawai')->insertGetId([
            'nama' => 'Siti Aminah',
            'jabatan' => 'Analis',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pegawai')->insert([
            'nama' => 'Budi Santoso',
            'jabatan' => 'Staf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('penilaian_kinerja')->insert([
            'pegawai_id' => $employeeId,
            'periode' => now()->format('Y-m'),
            'total' => 3.25,
            'updated_at' => now(),
        ]);

        $response = $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Selamat datang, hrd')
            ->assertSee('Data Pegawai')
            ->assertSee('Penilaian Kinerja')
            ->assertSee('3.25')
            ->assertSee('Siti Aminah')
            ->assertDontSee('Data Pelamar')
            ->assertDontSee('Profile Matching');
        $this->assertSame(1, substr_count($response->getContent(), 'Buat penilaian'));

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/pelamar')
            ->assertNotFound();

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/profile-matching')
            ->assertNotFound();
    }
}
