<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pelamar', 'upload_cv')) {
            Schema::table('pelamar', function (Blueprint $table): void {
                $table->string('upload_cv')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pelamar', 'upload_cv')) {
            Schema::table('pelamar', function (Blueprint $table): void {
                $table->dropColumn('upload_cv');
            });
        }
    }
};
