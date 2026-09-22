<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom biodata yang bisa diubah mahasiswa lewat halaman Profile Settings.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('entry_year');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('gender')->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('gender');
            $table->string('phone')->nullable()->after('address');
        });
    }

    /**
     * Batalkan penambahan kolom biodata.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'birth_date',
                'gender',
                'address',
                'phone',
            ]);
        });
    }
};
