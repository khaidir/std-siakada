<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nim')->unique();
            $table->foreignId('study_program_id')->constrained()->cascadeOnDelete();
            $table->string('entry_year');
            $table->string('status');
            $table->decimal('gpa', 5, 2)->default(0);
            $table->integer('total_sks')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
