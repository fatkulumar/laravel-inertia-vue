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
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('participant_id');
            $table->foreignUuid('committee_id');
            $table->string('status');
            $table->date('approval_date')->nullable();
            $table->date('graduation_date')->nullable();
            $table->text('file');
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('users');
            $table->foreign('committee_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
