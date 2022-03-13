<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group Файлы
 *
 * обработка файлов, загрузка и выгрузка
 */
class FileLoadController extends Controller
{
    /**
     * Выгрузка банка
     *
     *
     */
    public function unloadingBank(Bank $bank){

    }
    public function _unloading(Bank $bank)
    {

        $file_response = [];

        $btz = $bank;
        if ($btz) {


            $categories = $btz->categories; // вернет все категории для бтз


            foreach ($categories as $category) {


                array_push($file_response, '$' . 'CATEGORY: ' . $category['name'] . PHP_EOL . PHP_EOL);
                $questions = question::all()->where('category_id', $category['id']);

                foreach ($questions as $question) {

                    if ($question['type_question_id'] != 2)
                        array_push($file_response, $question['question'] . '{' . PHP_EOL);


                    switch ($question['type_question_id']) {
                        case 1://открытый вопрос
                            $opinions = $question['opinions'];

                            for ($i = 0, $count_true = 0; $i < count($question['opinions']); $i++) {
                                ($opinions[$i]['success'] == true) ? $count_true += 1 : $count_true;
                            }

                            if ($count_true > 1) {
                                //правильных ответов больше одного

                                $percent_question_true = round(100 / $count_true, 5);

                                if (count($question['opinions']) != $count_true) {
                                    $percent_question_false = round(100 / (count($question['opinions']) - $count_true), 5);
                                }

                                foreach ($opinions as $opinion) {

                                    if ($opinion['success'] == true) {
                                        array_push($file_response, '~%' . $percent_question_true . '% ' . $opinion['opinion'] . PHP_EOL);
                                    } else {
                                        array_push($file_response, '~%-' . $percent_question_false . '% ' . $opinion['opinion'] . PHP_EOL);
                                    }
                                }

                            } else {
                                // если значений одно
                                foreach ($opinions as $opinion) {
                                    if ($opinion['success'] != true) {
                                        array_push($file_response, '~' . $opinion['opinion'] . PHP_EOL);
                                    } else {
                                        array_push($file_response, '=' . $opinion['opinion'] . PHP_EOL);
                                    }

                                }
                            }

                            array_push($file_response, '}' . PHP_EOL . PHP_EOL);

                            break;
                        case 2://закрытый вопрос
                            $opinions = $question['question'];

                            preg_match_all("/@(.*?)@/", $opinions, $res);
                            $value_replace = ($res[1][0] == '') ? '{' : '{ =';


                            $first_position = strpos($opinions, "@");
                            $opinions = substr_replace($opinions, $value_replace, $first_position, 1);

                            $second_position = strpos($opinions, "@");
                            $opinions = substr_replace($opinions, "}", $second_position, 1);

                            $add_helper = strstr($opinions, '}', true);

                            foreach ($question['opinions'] as $opinion) {
                                $add_helper = $add_helper . " =" . $opinion['opinion'];
                            }

                            $add_helper = $add_helper . ' ' . strstr($opinions, '}');
                            array_push($file_response, $add_helper . PHP_EOL . PHP_EOL);

                            break;
                        case 3://на соответсвие
                            return "_____3";
                            break;
                        case 4://на упорядочивание вопрос
                            $opinions = collect($question['opinions'])->sort();

                            $i = 1;
                            foreach ($opinions as $opinion) {
                                array_push($file_response, '=' . $i . ' -> ' . $opinion['opinion'] . PHP_EOL);
                                $i += 1;
                            }
                            array_push($file_response, '}' . PHP_EOL . PHP_EOL);

                            break;
                    }
                }
            }

            Storage::disk('local')->put('Btz/TEST.txt', $file_response);

            $headers = [
                'Content-Type' => 'application/txt',
            ];

            return response()->download('../storage/app/Btz/TEST.txt', $btz['name'] . ".txt", $headers);
        }
        return response()->json(["success" => false,
            "error" => "БТЗ не найдено"], 404);

    }

}
