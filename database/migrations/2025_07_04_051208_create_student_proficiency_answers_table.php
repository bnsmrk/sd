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
        Schema::create('student_proficiency_answers', function (Blueprint $table) {
            $table->id();
              $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('proficiency_test_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('proficiency_questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_proficiency_answers');
    }
};
