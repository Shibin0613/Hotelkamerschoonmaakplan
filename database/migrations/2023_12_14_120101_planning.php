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
        Schema::create('planning', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_id');
            $table->json('element');
            $table->timestamp('datetime');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('house_id')->references('id')->on('houses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning');
    }
};
