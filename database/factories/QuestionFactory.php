<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition($type = 1)
    {
        return [
            'category_id' => Category::query()->inRandomOrder()->first()->value('id'),
        ];

    }

    /**
     * Если стоит значение 0, то будет заполняться случайными значениями, заполнять в диапозоне от 1 до 4
    */
    public function typeQuestion($typeId = 0)
    {
        return $this->state(function (array $attribute) use ($typeId) {

            if ($typeId == 0){
                $typeId = $this->faker->numberBetween(1, 4);
            }

            switch ($typeId) {
                case 0:
                case 1:
                    return [
                        'type_question_id' => 1,
                        'question' => "Сколько бит в байте",
                        'answer' => [1],
                        'opinions' => [
                            [
                                "id" => 1,
                                "opinion" => "8"
                            ],
                            [
                                "id" => 2,
                                "opinion" => "4"
                            ],
                        ],
                    ];
                case 2:
                    return [
                        'type_question_id' => 2,
                        'question' => "В одном байте @@ бит",
                        'answer' => ["8"],
                    ];
                case 3:
                    return [
                        'type_question_id' => 3,
                        'question' => "Сопоставьте список ниже",
                        'answer' => [
                            [
                                "id_opinion" => 1,
                                "id_answer" => 2
                            ],
                            [
                                "id_opinion" => 2,
                                "id_answer" => 1
                            ]
                        ],
                        'opinions' => [
                            [
                                "opinions" => [
                                    [
                                        "id" => 1,
                                        "opinion" => "четыре"
                                    ],
                                    [
                                        "id" => 2,
                                        "opinion" => "пять"
                                    ]
                                ],
                                "answers" => [
                                    [
                                        "id" => 1,
                                        "opinion" => "6-1"
                                    ],
                                    [
                                        "id" => 2,
                                        "opinion" => "2+2"
                                    ]
                                ]
                            ]
                        ]
                    ];
                case 4:
                    return [
                        'type_question_id' => 4,
                        'question' => "Сопоставьте список в порядке убывания",
                        'answer' => [1, 2, 3],
                        'opinions' => [
                            [
                                "id" => 3,
                                "opinion" => "Отключить плиту"
                            ],
                            [
                                "id" => 1,
                                "opinion" => "Включить плиту"
                            ],
                            [
                                "id" => 2,
                                "opinion" => "Приготовить завтрак"
                            ]
                        ],
                    ];
            }
        });
    }
}
