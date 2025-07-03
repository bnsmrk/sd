<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proficiency_tests', function (Blueprint $table) {
            $table->id();
             $table->string('title');
            $table->enum('type', ['reading', 'numerical']);
            $table->foreignId('year_level_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();

            $table->timestamp('scheduled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proficiency_tests');
    }
};
