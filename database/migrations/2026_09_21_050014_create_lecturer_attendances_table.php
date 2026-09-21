<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturer_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->index(['lecturer_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturer_attendances');
    }
};
