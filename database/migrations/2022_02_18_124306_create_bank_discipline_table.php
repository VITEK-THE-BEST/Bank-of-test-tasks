<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_discipline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discipline_id')->nullable()->default(null);
            $table->foreign('discipline_id')->references('id')->on('disciplines')->cascadeOnDelete();

            $table->unsignedBigInteger('bank_id');
            $table->foreign('bank_id')->references('id')->on('banks')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_discipline');
    }
};
