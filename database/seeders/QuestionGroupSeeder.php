<?php

namespace Database\Seeders;

use App\Models\QuestionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        QuestionGroup::insert(
            ['name' => "Открытых"],
        );
        QuestionGroup::insert(
            ['name' => "Закрытых"],
        );
        QuestionGroup::insert(
            ['name' => "На соответствие"],
        );
        QuestionGroup::insert(
            ['name' => "На упорядочивание"],
        );
    }
}
