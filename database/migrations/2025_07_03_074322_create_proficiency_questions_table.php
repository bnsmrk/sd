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
        Schema::create('proficiency_questions', function (Blueprint $table) {
            $table->id();
             $table->foreignId('proficiency_test_id')->constrained()->onDelete('cascade');
            $table->string('question');
            $table->enum('type', ['multiple_choice', 'checkboxes', 'true_false', 'essay', 'fill_in_blank']);
            $table->json('options')->nullable();
            $table->json('answer_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proficiency_questions');
    }
};
