<?php

namespace Database\Seeders;

use App\Models\TypeQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeQuestion::insert(
            [
                'question_group_id' => 1,
                "name" => "1 тип вопроса"
            ]
        );
        TypeQuestion::insert(
            [
                'question_group_id' => 2,
                'name' => "2 тип вопроса"
            ],
        );
        TypeQuestion::insert(
            [
                'question_group_id' => 3,
                'name' => "3 тип вопроса"
            ]
        );
        TypeQuestion::insert(
            [
                'question_group_id' => 4,
                'name' => "4 тип вопроса"
            ],
        );
    }
}
