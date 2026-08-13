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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('prot', 7, 4)->default(0);
            $table->decimal('fat', 7, 4)->default(0);
            $table->decimal('carb', 7, 4)->default(0);
            $table->unsignedTinyInteger('gi')->default(50);

            $table->float('weight')->default(100)
                ->comment('Complex product might have the weight different from 100 g');

            $table->unsignedMediumInteger('used')->default(0);
            //$table->boolean('is_complex')->default(false);

            ///$table->foreignId('product_group_id')->constrained('product_groups')->onDelete('cascade');

            $table->unsignedBigInteger('product_group_id');

            $table->foreign('product_group_id')
                ->references('id')
                ->on('product_groups')
                ->onDelete('cascade');

            //to itself
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
