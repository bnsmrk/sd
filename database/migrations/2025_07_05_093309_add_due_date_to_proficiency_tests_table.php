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
        Schema::table('proficiency_tests', function (Blueprint $table) {
             $table->timestamp('due_date')->nullable()->after('scheduled_at');
        });
    }

    /**s
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proficiency_tests', function (Blueprint $table) {
           $table->dropColumn('due_date');
        });
    }
};
