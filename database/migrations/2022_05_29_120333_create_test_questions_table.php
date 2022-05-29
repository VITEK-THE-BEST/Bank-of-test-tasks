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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_test_id');
            $table->foreign('user_test_id')->references('id')->on('user_tests')->cascadeOnDelete();

            $table->unsignedBigInteger('type_question_id');
            $table->foreign('type_question_id')->references('id')->on('type_questions');

            $table->string("question");
            $table->json("answer");
            $table->json("opinions")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_questions');
    }
};
