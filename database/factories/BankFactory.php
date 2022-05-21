<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id'=>User::query()->inRandomOrder()->first()->value('id'),
            'name'=>$this->faker->company(),
            'start_testing'=>$this->faker->date(),
            'end_testing'=>$this->faker->date(),

        ];
    }
}
