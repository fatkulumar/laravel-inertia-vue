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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('regional_id');
            $table->uuid('committee_id');
            $table->uuid('category_id'); // kategori kelas
            $table->uuid('class_room_id');
            $table->uuid('chief_id'); //ketua pelaksana
            $table->uuid('type_activity_id');
            $table->uuid('regency_regional_id');
            $table->bigInteger('periode');
            $table->text('poster'); //konsep kegiatan
            $table->text('concept'); //konsep kegiatan
            $table->text('committee_layout'); //susunan panitia
            $table->text('target_participant'); //target peserta
            $table->uuid('speaker_id'); //pemateri
            $table->integer('total_activity'); // total kegiatan yang sudah dikerjakan
            $table->integer('price'); // harga
            $table->text('facility')->nullable(); // fasiliitas // total fasilitas
            $table->integer('total_rooms_stay')->nullable(); // jumlah ruang menginap
            $table->text('benefit'); //
            $table->string('location');
            $table->text('google_maps');
            $table->text('address');
            $table->enum('status', ['pending', 'approval', 'accepted', 'rejected', 'received' ,'overview'])->default('pending');
            $table->dateTime('start_date_class');
            $table->dateTime('end_date_class');
            $table->date('approval_date')->nullable();
            $table->date('graduation_date')->nullable();
            // $table->text('proposal');
            $table->dateTime('date_overview')->nullable();
            $table->dateTime('date_received')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
