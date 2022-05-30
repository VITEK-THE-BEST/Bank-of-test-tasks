<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PassedTest;
use App\Models\TestQuestion;
use App\Models\UserTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Str;

/**
 * @unauthenticated
 * @group Прохождение тестирования
 *
 *
 */
class PassTestController extends Controller
{
    /**
     * Начать проходить тестирование
     *
     * тестирование будет доступно во время начала тестирвоания
     * */
    public function start(UserTest $userTest)
    {
        $nowDay = Carbon::now();
        $start_testing = Carbon::parse($userTest['start_testing']);
        $end_testing = Carbon::parse($userTest['end_testing']);
        if ($nowDay->gte($start_testing)) {
            if ($end_testing->gte($nowDay)) {
                $questions = $userTest
                    ->test_questions()
                    ->select([
                        'id',
                        'type_question_id',
                        'question',
                        'opinions',
                    ])
                    ->get();
//                TODO: зарандомить вывод вариантов ответов
                return response()->json($questions);
            }
            return response()->json(['error' => 'тестирование окончено'], 403);
        }
        return response()->json(['error' => 'тестирование будет доступно:' . $start_testing->toDateTimeString()], 403);
    }

    /**
     * завершение тестирования
     *
     * тестирование возможно завершить в определенное время
     * */
    public function end(Request $request, UserTest $userTest)
    {
        $validate = $request->validate([
            'answers' => 'required',
        ]);

        $nowDay = Carbon::now();
        $start_testing = Carbon::parse($userTest['start_testing']);
        $end_testing = Carbon::parse($userTest['end_testing']);

        if ($nowDay->gte($start_testing)) {
            if ($end_testing->gte($nowDay)) {

                foreach ($validate['answers'] as $answer_key => $answer) {
                    $question = TestQuestion::query()->where('user_test_id', $userTest->id)->findOrFail($answer['id']);

                    switch ($question['type_question_id']) {
                        case 1 ://открытый вопрос (один правильный ответ)

                            if ($answer['answer'] == $question['answer']) {
                                $validate['answers'][$answer_key]['result'] = 100;
                            } else {
                                $validate['answers'][$answer_key]['result'] = 0;
                            }

                            break;
                        case 2 :// закрытый вопрос, возможно множество ответов
                            $answer_lower = array_map(function ($v) {
                                return Str::lower($v);
                            }, $answer['answer']);

                            $question_lower = array_map(function ($v) {
                                return Str::lower($v);
                            }, $question['answer']);

                            $result = count(array_intersect($answer_lower, $question_lower));//колличесто правильных ответов
                            if ($result != 0) {
                                $validate['answers'][$answer_key]['result'] = 100;
                            } else {
                                $validate['answers'][$answer_key]['result'] = 0;
                            }

                            break;
                        case 3 :// на соответсвие
                            $count_answers = 0;
                            foreach ($answer['answer'] as $value) {
                                if (in_array($value, $question['answer'])) {
                                    $count_answers += 1;
                                }
                            }

                            if ($count_answers != 0) {
                                $validate['answers'][$answer_key]['result'] = ($count_answers / count($question['answer'])) * 100;
                            } else {
                                $validate['answers'][$answer_key]['result'] = 100;
                            }


                            break;
                        case 4:// на соответсвие
                            $count_answers = 0;
                            foreach ($answer['answer'] as $key_a => $value_a) {
                                foreach ($question['answer'] as $key_q => $value_q) {
                                    if ($key_a == $key_q) {
                                        if ($value_a == $value_q) {
                                            $count_answers++;
                                        }
                                    }
                                }
                            }
                            if ($count_answers != 0) {
                                $validate['answers'][$answer_key]['result'] = ($count_answers / count($question['answer'])) * 100;
                            } else {
                                $validate['answers'][$answer_key]['result'] = 100;
                            }

                            break;
                    }
                }
                $question_count = TestQuestion::query()->where('user_test_id', $userTest->id)->count();

                $answers = array_column($validate['answers'], 'result');
                $total_result = 0;

                foreach ($answers as $answer) {
                    $total_result += $answer;
                }

                $assessment = $total_result != 0 ? $total_result / $question_count : 0;

                PassedTest::query()->create([
                    'user_test_id' => $userTest->id,
                    'assessment' => $assessment,
                    'result' => $validate['answers'],
                    'end_testing' => Carbon::now(),
                ]);

                return response()->json([]);
            }
            return response()->json(['error' => 'тестирование окончено'], 403);
        }
        return response()->json(['error' => 'тестирование будет доступно:' . $start_testing->toDateTimeString()], 403);
    }

}
