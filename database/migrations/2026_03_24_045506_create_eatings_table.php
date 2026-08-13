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
        Schema::create('eatings', function (Blueprint $table) {
            $table->id();
            $table->float('k1')->default(1);
            $table->float('k2')->default(0);
            $table->float('k3')->default(3);
            $table->float('gl1')->default(5.6);
            $table->float('gl2')->default(5.6);
            $table->float('be')->default(10);
            $table->integer('eaten')->default(0);
            $table->date('eaten_date')->default(null);

            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eatings');
    }
};
