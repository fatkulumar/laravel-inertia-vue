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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('profileable_id');
            $table->string('profileable_type');
            $table->foreignUuid('regional_id');
            $table->foreignUuid('address')->nullable();
            $table->string('hp', 13)->nullable();
            $table->enum('gender', ['laki-laki', 'perempuan'])->nullable();
            $table->timestamps();

            $table->foreign('regional_id')->references('id')->on('regionals');
            $table->foreign('profileable_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
