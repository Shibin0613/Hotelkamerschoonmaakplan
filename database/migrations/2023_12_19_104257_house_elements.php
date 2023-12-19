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
        Schema::create("house_elements", function (Blueprint $table) {
            $table->unsignedBigInteger('house_id');
            $table->unsignedBigInteger('element_id');

            // Assign foreign keys
            $table->foreign('house_id')->references('id')->on('houses');
            $table->foreign('element_id')->references('id')->on('elements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
