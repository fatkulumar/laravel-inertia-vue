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
        Schema::create('guide_cadres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('type_activity_id');
            $table->string('name');
            $table->text('link');
            $table->string('information');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_cadres');
    }
};
