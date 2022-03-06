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
        TypeQuestion::inseart([
            [
                'question_group_id' => 1,
                "name" => "1 тип вопроса"
            ],
            ['name' => "2 тип вопроса"],
            ['name' => "3 тип вопроса"],
            ['name' => "4 тип вопроса"],
        ]);
    }
}
