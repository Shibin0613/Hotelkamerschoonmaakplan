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
        Schema::create('damage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planning_id');
            $table->string('name');
            $table->integer('status');
            $table->integer('need');
            $table->string('repair');
            $table->timestamp('datetime');

            $table->foreign('planning_id')->references('id')->on('planning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage');
    }
};
