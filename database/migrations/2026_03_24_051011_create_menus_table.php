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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->decimal('prot', 7, 4)->default(0);
            $table->decimal('fat', 7, 4)->default(0);
            $table->decimal('carb', 7, 4)->default(0);
            $table->unsignedTinyInteger('gi')->default(50);

            $table->float('weight')->default(0);
            $table->boolean('is_snack')->default(false);

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
        Schema::dropIfExists('menus');
    }
};
