<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * Выгружается в формате gift
     */
    public function unloadingBank(Bank $bank)
    {
        $bankName = '$CATEGORY: ' . $bank['name'] . '/';
        $giftFile = [];


        foreach ($bank->sections as $section) {
            foreach ($section->categories as $category) {
                $CATEGORY_NAME = $bankName . $section['name'] . '/' . $category['name'];

                array_push($giftFile, $CATEGORY_NAME . PHP_EOL . PHP_EOL);

                foreach ($category->questions as $question) {
                    switch ($question['type_question_id']) {
                        case 1://открытый вопрос (один правильный ответ)
                            array_push($giftFile, $question['question'] . '{' . PHP_EOL);

                            foreach ($question['opinions'] as $opinion) {
                                if ($opinion['id'] == $question['answer'][0]) {
                                    array_push($giftFile, "=" . $opinion['opinion'] . PHP_EOL);
                                } else {
                                    array_push($giftFile, "~" . $opinion['opinion'] . PHP_EOL);
                                }
                            }

                            array_push($giftFile, '}' . PHP_EOL . PHP_EOL);
                            break;
                        case 2: // закрытый вопрос, возможно указать только один вариант на подстановку
                            foreach ($question['answer'] as $answer) {
                                $position = strpos($question['question'], "@@");
                                $question['question'] = substr_replace($question['question'], "{ =" . $answer . " }", $position, 2);
                            }
                            array_push($giftFile, $question['question'] . PHP_EOL . PHP_EOL);
                            break;
                        case 3: // на соответсвие
                            array_push($giftFile, $question['question'] . '{' . PHP_EOL);
                            foreach ($question['answer'] as $answer) {
                                foreach ($question['opinions'][0]['opinions'] as $opinion_opinion) {
                                    if ($opinion_opinion['id'] == $answer['id_opinion']) {
                                        $question_opinion = $opinion_opinion['opinion'];
                                        break;
                                    }
                                }
                                foreach ($question['opinions'][0]['answers'] as $opinion_answer) {
                                    if ($opinion_answer['id'] == $answer['id_answer']) {
                                        array_push($giftFile, "=" . $question_opinion . " -> " . $opinion_answer['opinion'] . PHP_EOL);
                                        break;
                                    }
                                }
                            }
                            array_push($giftFile, '}' . PHP_EOL . PHP_EOL);
                            break;
                        case 4://на упорядочивание
                            array_push($giftFile, $question['question'] . '{>0 ALL VERTICAL ABSOLUTE_POSITION SHOW none' . PHP_EOL);

                            foreach ($question['answer'] as $answer) {
                                foreach ($question['opinions'] as $opinion) {
                                    if ($opinion['id'] == $answer) {
                                        array_push($giftFile, $opinion['opinion'] . PHP_EOL);
                                        break;
                                    }

                                }
                            }
                            array_push($giftFile, '}' . PHP_EOL . PHP_EOL);
                            break;
                    }
                }
            }
        }

        $headers = [
            'Content-Type' => 'application/txt',
        ];

        return response()->streamDownload(function () use ($giftFile) {
            echo implode('',$giftFile);
        }, 'laravel-readme.txt',$headers);
    }
}
