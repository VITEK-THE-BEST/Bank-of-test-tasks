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
        Schema::create('passed_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_test_id')->nullable()->default(null);
            $table->foreign('user_test_id')->references('id')->on('user_tests')->cascadeOnDelete();


            $table->float("assessment",5);
            $table->json("result");
            $table->date("start_testing")->nullable();
            $table->date("end_testing")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
