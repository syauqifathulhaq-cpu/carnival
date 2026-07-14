<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotor_id')->constrained('promotors')->onDelete('cascade');
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->dateTime('event_date');
            $table->integer('max_tickets_per_nik')->default(1);
            $table->string('banner_image_path')->nullable();
            $table->enum('status_event', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
