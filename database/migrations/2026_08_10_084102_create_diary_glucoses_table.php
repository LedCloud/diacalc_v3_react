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
        Schema::create('diary_glucoses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diary_id')->nullable(false);
            $table->float('gl')->default(5.6);

            $table->foreign('diary_id')
                ->references('id')
                ->on('diary')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_glucoses');
    }
};
