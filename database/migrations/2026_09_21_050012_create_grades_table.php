<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_plan_detail_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
            $table->decimal('assignment_score', 5, 2)->nullable();
            $table->decimal('midterm_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->decimal('score', 5, 2);
            $table->string('letter_grade', 2);
            $table->decimal('grade_point', 4, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
