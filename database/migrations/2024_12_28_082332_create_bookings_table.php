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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('user_id');
            $table->string('meeting_name');
            $table->string('description')->nullable();
            $table->string('request')->nullable();
            $table->string('status');
            $table->date('booked_at');
            $table->time('booked_from');
            $table->time('booked_to');
            $table->timestamps();

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->cascadeOnDelete();
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
