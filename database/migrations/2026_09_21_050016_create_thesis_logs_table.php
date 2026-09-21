<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('activity');
            $table->text('notes');
            $table->boolean('supervisor_approval')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_logs');
    }
};
