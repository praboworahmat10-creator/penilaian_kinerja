<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password');
        });

        DB::table('users')->insert([
            'username' => 'hrd',
            'password' => md5('secret'),
        ]);

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

    public function test_login_page_is_rendered(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Selamat Datang')
            ->assertSee('Masuk');
    }

    public function test_legacy_password_login_is_upgraded_and_opens_dashboard(): void
    {
        $this->post('/login', ['username' => 'hrd', 'password' => 'secret'])
            ->assertRedirect(route('dashboard'));

        $storedPassword = DB::table('users')->where('username', 'hrd')->value('password');
        $this->assertTrue(Hash::check('secret', $storedPassword));

        $this->get('/dashboard')->assertOk()->assertSee('Selamat datang, hrd');
    }

    public function test_invalid_login_is_rejected(): void
    {
        $this->from('/')
            ->post('/login', ['username' => 'hrd', 'password' => 'wrong'])
            ->assertRedirect('/')
            ->assertSessionHasErrors('username');
    }
}
