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
        Schema::create('diary_meal_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('prot', 7, 4)->default(0);
            $table->decimal('fat', 7, 4)->default(0);
            $table->decimal('carb', 7, 4)->default(0);
            $table->unsignedTinyInteger('gi')->default(50);
            $table->float('weight')->default(100);

            $table->unsignedBigInteger('diary_meal_id')->nullable(false);

            $table->foreign('diary_meal_id')
                ->references('id')
                ->on('diary_meals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_meal_products');
    }
};
