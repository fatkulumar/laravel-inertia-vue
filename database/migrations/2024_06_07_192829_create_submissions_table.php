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
            $table->foreignUuid('participant_id')->nullable();
            $table->foreignUuid('committee_id');
            $table->foreignUuid('category_id');
            $table->foreignUuid('class_room_id');
            $table->bigInteger('periode');
            $table->string('location');
            $table->text('google_maps');
            $table->text('address');
            $table->string('status');
            $table->date('start_date_class');
            $table->date('end_date_class');
            $table->date('approval_date')->nullable();
            $table->date('graduation_date')->nullable();
            $table->text('file');
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('users');
            $table->foreign('committee_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('class_room_id')->references('id')->on('class_rooms');
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
