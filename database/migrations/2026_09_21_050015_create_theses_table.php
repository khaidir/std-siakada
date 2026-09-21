<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('abstract');
            $table->foreignId('supervisor_1_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('supervisor_2_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->string('status');
            $table->date('submission_date');
            $table->timestamps();

            $table->index('supervisor_1_id');
            $table->index('supervisor_2_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
