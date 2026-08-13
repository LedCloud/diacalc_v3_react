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
        Schema::create('diary_meals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diary_id')->nullable(false);

            $table->float('gl1')->default(5.6);
            $table->float('gl2')->default(5.6);
            $table->float('k1')->default(1);
            $table->float('k2')->default(0);
            $table->float('k3')->default(3);

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
        Schema::dropIfExists('diary_meals');
    }
};
