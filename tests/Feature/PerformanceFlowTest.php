<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerformanceFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['detail_penilaian_kinerja', 'penilaian_kinerja', 'kategori_kinerja', 'pegawai'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('pegawai', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('kategori_kinerja', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('kode', 40);
            $table->string('nama', 120);
            $table->unsignedTinyInteger('bobot');
            $table->text('indikator');
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
        });

        Schema::create('penilaian_kinerja', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('pegawai_id');
            $table->char('periode', 7);
            $table->string('penilai', 50);
            $table->text('catatan')->nullable();
            $table->decimal('total', 5, 2);
            $table->timestamps();
            $table->unique(['pegawai_id', 'periode']);
        });

        Schema::create('detail_penilaian_kinerja', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('penilaian_id');
            $table->unsignedInteger('kategori_id');
            $table->unsignedTinyInteger('nilai');
            $table->timestamps();
            $table->unique(['penilaian_id', 'kategori_id']);
        });

        $categories = [
            ['kualitas', 'Kualitas Hasil Kerja', 25],
            ['produktivitas', 'Produktivitas', 20],
            ['disiplin_sop', 'Disiplin & Kepatuhan SOP', 20],
            ['ketepatan_waktu', 'Ketepatan Waktu', 15],
            ['kerja_sama', 'Kerja Sama & Komunikasi', 10],
            ['tanggung_jawab', 'Tanggung Jawab & Inisiatif', 10],
        ];

        foreach ($categories as $index => [$code, $name, $weight]) {
            DB::table('kategori_kinerja')->insert([
                'kode' => $code,
                'nama' => $name,
                'bobot' => $weight,
                'indikator' => 'Indikator '.$name,
                'urutan' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_employee_can_be_assessed_and_scores_are_weighted_by_category(): void
    {
        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/kategori-kinerja')
            ->assertOk()
            ->assertSee('Kualitas Hasil Kerja')
            ->assertSee('25%');

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/pegawai/create')
            ->assertOk()
            ->assertSee('Tambah Pegawai');

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/pegawai?search=Analis')
            ->assertOk()
            ->assertSee('Cari pegawai');

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->post('/pegawai', [
                'nama' => 'Dina Putri',
                'jabatan' => 'Analis',
                'no_hp' => '08123456789',
                'email' => 'dina@example.test',
            ])
            ->assertRedirect(route('employees.index'));

        $employeeId = DB::table('pegawai')->where('nama', 'Dina Putri')->value('id');
        $categoryIds = DB::table('kategori_kinerja')->orderBy('urutan')->pluck('id');
        $scores = [4, 3, 2, 4, 3, 2];
        $ratings = [];
        foreach ($categoryIds as $index => $categoryId) {
            $ratings[$categoryId] = $scores[$index];
        }

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->post('/penilaian-kinerja', [
                'pegawai_id' => $employeeId,
                'periode' => '2026-11',
                'nilai' => $ratings,
                'catatan' => 'Target tercapai.',
            ])
            ->assertRedirect(route('performance.index'));

        $assessment = DB::table('penilaian_kinerja')->first();
        $this->assertNotNull($assessment);
        $this->assertEquals(3.1, (float) $assessment->total);
        $this->assertSame(6, DB::table('detail_penilaian_kinerja')->count());

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get(route('performance.show', $assessment->id))
            ->assertOk()
            ->assertSee('3.10')
            ->assertSee('Kualitas Hasil Kerja');

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->get('/penilaian-kinerja?search=Dina&periode=2026-11')
            ->assertOk()
            ->assertSee('Dina Putri')
            ->assertSee('2026-11');

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->delete(route('employees.destroy', $employeeId))
            ->assertSessionHasErrors('pegawai');
        $this->assertDatabaseHas('pegawai', ['id' => $employeeId]);
    }

    public function test_resubmitting_same_employee_and_period_updates_instead_of_duplicating(): void
    {
        $employeeId = DB::table('pegawai')->insertGetId([
            'nama' => 'Dina Putri',
            'jabatan' => 'Analis',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryIds = DB::table('kategori_kinerja')->orderBy('urutan')->pluck('id');
        $ratings = [];
        foreach ($categoryIds as $categoryId) {
            $ratings[$categoryId] = 4;
        }
        $payload = [
            'pegawai_id' => $employeeId,
            'periode' => '2026-11',
            'nilai' => $ratings,
        ];

        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->post('/penilaian-kinerja', $payload)
            ->assertRedirect(route('performance.index'));
        $this->withSession(['user_id' => 1, 'username' => 'hrd'])
            ->post('/penilaian-kinerja', $payload)
            ->assertRedirect(route('performance.index'));

        $this->assertSame(1, DB::table('penilaian_kinerja')->count());
        $this->assertSame(6, DB::table('detail_penilaian_kinerja')->count());
        $this->assertEquals(4, (float) DB::table('penilaian_kinerja')->value('total'));
    }
}
