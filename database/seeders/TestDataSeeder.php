<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Category;
use App\Models\Question;
use App\Models\Section;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserFactory::new()->has(
            Bank::factory(5)
                ->has(
                    Section::factory(5)
                        ->hasAttached(
                            Category::factory(5)
//                                ->has(
//                                    Question::factory(16)->typeQuestion()
//                                )
                        )
                )
        )->create([
            'email' => "test@mail.ru",

        ]);
    }
}
