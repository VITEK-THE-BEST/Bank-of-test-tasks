<?php

namespace Database\Seeders;

use App\Models\QuestionGroup;
use Database\Factories\QuestionGroupFactory;
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
        QuestionGroupFactory::new()->createMany([
            ['name' => "Открытых"],
            ['name' => "Закрытых"],
            ['name' => "На соответствие"],
            ['name' => "На упорядочивание"],
        ]);
    }
}
