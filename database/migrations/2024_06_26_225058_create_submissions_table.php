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
            $table->foreignUuid('category_id'); // kategori kelas
            $table->foreignUuid('class_room_id');
            $table->foreignUuid('chief_id'); //ketua pelaksana
            $table->foreignUuid('type_activity_id'); //ketua pelaksana
            $table->bigInteger('periode');
            $table->text('poster'); //konsep kegiatan
            $table->text('concept'); //konsep kegiatan
            $table->text('committee_layout'); //susunan panitia
            $table->text('target_participant'); //target peserta
            $table->string('speaker')->nullable(); //pemateri
            $table->integer('total_activity'); // total kegiatan yang sudah dikerjakan
            $table->integer('price'); // harga
            $table->text('facility')->nullable(); // fasiliitas // total fasilitas
            $table->integer('total_rooms_stay')->nullable(); // jumlah ruang menginap
            $table->text('benefit'); // jumlah ruang menginap
            $table->string('location');
            $table->text('google_maps');
            $table->text('address');
            $table->string('status');
            $table->dateTime('start_date_class');
            $table->dateTime('end_date_class');
            $table->date('approval_date')->nullable();
            $table->date('graduation_date')->nullable();
            $table->text('file');
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('users');
            $table->foreign('committee_id')->references('id')->on('users');
            $table->foreign('chief_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('class_room_id')->references('id')->on('class_rooms');
            $table->foreign('type_activity_id')->references('id')->on('type_activities');
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
