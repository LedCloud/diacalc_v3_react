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
        Schema::create('default_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('name', 255);

            //max possible value is 100.0000
            $table->decimal('prot', 7, 4)->default(0);
            $table->decimal('fat', 7, 4)->default(0);
            $table->decimal('carb', 7, 4)->default(0);
            $table->unsignedTinyInteger('gi')->default(50);

            $table->foreign('group_id')
                ->references('id')
                ->on('default_groups')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_products');
    }
};
