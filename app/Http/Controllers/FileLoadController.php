<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

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
//        $symbols_replace = ["\\", "{", "}"];


        foreach ($bank->sections as $section) {
            foreach ($section->categories as $category) {
                $CATEGORY_NAME = $bankName . $section['name'] . '/' . $category['name'];
                array_push($giftFile, $CATEGORY_NAME . PHP_EOL . PHP_EOL);

                foreach ($category->questions as $question) {
//                    foreach ($symbols_replace as $symbol) {
//                        $question['question'] = str_replace($symbol, '\\' . $symbol, $question['question']);
//                    }
                    preg_quote($question['question']);

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
                        case 2: // закрытый вопрос, возможно множество ответов

                            if (preg_match("!@(.*?)@!si", $question['question'], $matches)) {
                                $question['question'] = str_replace($matches[0], "@@", $question['question']);
                            }

                            $position = strpos($question['question'], "@@");
                            $answer_val = '';

                            foreach ($question['answer'] as $answer) {
                                $answer_val = $answer_val . "=%100%" . $answer . "# ";
                            }
                            $question['question'] = str_replace("@@", "{ " . $answer_val . " }", $question['question']);

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
            echo implode('', $giftFile);
        }, 'laravel-readme.txt', $headers);
    }

    /**
     * Загрузка банка
     *
     * Загрузка в формате Moodle XML
     */
    public function loadingBank(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required'
        ]);

        $array_file = json_decode(json_encode(simplexml_load_file($validated['file'])), TRUE);

        $query_questions = [];

        $bank_name = '';
        $section_name = '';
        $category_name = '';
        foreach ($array_file['question'] as $question_obj) {
            if (array_key_exists('category', $question_obj)) {
                $name_categories = explode("/", $question_obj['category']['text']);

                if (count($name_categories) == 5) {
                    if ($query_questions != []) {
                        $category->questions()->saveMany($query_questions);
                        $query_questions = [];
                    }

                    if ($name_categories[2] != $bank_name) {
                        $bank = Bank::query()->create([
                            "name" => $name_categories[2],
                            'user_id' => auth()->id(),
//                            'start_testing' => '00.00.00',
//                            'end_testing' => '00.00.00'
                        ]);
                        $bank_name = $name_categories[2];
                    }
                    if ($name_categories[3] != $section_name) {
                        $section = Section::query()->create([
                            "name" => $name_categories[3],
                            'bank_id' => $bank->id
                        ]);
                        $section_name = $name_categories[3];
                    }
                    if ($name_categories[4] != $category_name) {

                        $category = Category::query()->create([
                            "name" => $name_categories[4],
                            "user_id" => auth()->id()
                        ]);
                        $category_name = $name_categories[4];
                    }


                    $section->categories()->syncWithoutDetaching($category);
                }
            }

            switch ($question_obj['@attributes']['type']) {
                case 'matching': // на соответсвие type_question 3
                    $question_text = $question_obj['questiontext']['text'];
                    $opinions_id_count = 1;
                    $answers_id_count = 1;
                    $opinions_opinions_arr = [];
                    $opinions_answers_arr = [];
                    $answer_arr = [];

                    foreach ($question_obj['subquestion'] as $opinion) {
                        array_push($opinions_opinions_arr, [
                            'id' => $opinions_id_count,
                            'opinion' => $opinion['text']
                        ]);
                        array_push($opinions_answers_arr, [
                            'id' => $answers_id_count,
                            'opinion' => $opinion['answer']['text']
                        ]);

                        array_push($answer_arr, [
                            'id_opinion' => $opinions_id_count,
                            'id_answer' => $answers_id_count,
                        ]);
                        $opinions_id_count++;
                        $answers_id_count++;
                    }

                    array_push($query_questions, new Question([
                        'type_question_id' => 3,
                        'category_id' => $category['id'],
                        'question' => $question_text,
                        'answer' => $answer_arr,
                        'opinions' => [
                            [
                                'opinions' => $opinions_opinions_arr,
                                'answers' => $opinions_answers_arr
                            ]
                        ],
                    ]));
                    break;
                case 'multichoice'://открытый вопрос, один вариант ответа
                    $question_text = $question_obj['questiontext']['text'];
                    $opinions_arr = [];
                    $answer_arr = [];
                    $opinions_id_count = 1;
                    foreach ($question_obj['answer'] as $answer) {
                        array_push($opinions_arr, [
                            'id' => $opinions_id_count,
                            'opinion' => $answer['text']
                        ]);
                        if ($answer['@attributes']['fraction'] == 100) {
                            array_push($answer_arr, $opinions_id_count);
                        }
                        $opinions_id_count++;
                    }

                    array_push($query_questions, new Question([
                        'type_question_id' => 1,
                        'category_id' => $category['id'],
                        'question' => $question_text,
                        'answer' => $answer_arr,
                        'opinions' => $opinions_arr,
                    ]));

                    break;
                case 'ordering'://на упорядочивание type_question 4
                    $question_text = $question_obj['questiontext']['text'];
                    $opinions_arr = [];
                    $answer_arr = [];
                    $opinions_id_count = 1;
                    foreach ($question_obj['answer'] as $answer) {
//                        return $answer['text'];
                        array_push($answer_arr, $opinions_id_count);
                        array_push($opinions_arr, [
                            'id' => $opinions_id_count,
                            'opinion' => $answer['text'],
                        ]);
                        $opinions_id_count++;
                    }

                    array_push($query_questions, new Question([
                        'type_question_id' => 4,
                        'category_id' => $category['id'],
                        'question' => $question_text,
                        'answer' => $answer_arr,
                        'opinions' => $opinions_arr,
                    ]));

                    break;
                case 'shortanswer': // закрытый вопрос
                    $question_text = $question_obj['questiontext']['text'];

                    $opinions_arr = [];
                    $answer_arr = [];
                    $question_text = str_replace('_____', '@@', $question_text);

                    array_push($answer_arr, $question_obj['answer']['text']);

                    array_push($query_questions, new Question([
                        'type_question_id' => 2,
                        'category_id' => $category['id'],
                        'question' => $question_text,
                        'answer' => $answer_arr,
                    ]));

                    break;
            }

        }
        return response()->json([]);
    }

    public function passport(Request $request, Bank $bank)
    {
        $validate = $request->validate([
            'scope_btz' => 'required',//входящая диагностика , текущий контроль ,промежуточный контроль (зачет), промежуточный контроль (экзамен), контроль остаточных знаний
            'time_testing' => 'required',//время провдение тестирования в минутах
            'difficulty_level' => 'required',//(базовый, повышенный, высокий)
            'max_score' => 'required',//максимальная оценка
        ]);

        $month = ['01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' => 'Апреля', '05' => 'Май', '06' => 'Июнь', '07' => 'Июль', '08' => 'Август', '09' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь'];
        $templateProcessor = new TemplateProcessor('../storage/resourse/test.docx');

        $templateProcessor->setValue('scope_btz', $validate['scope_btz']);
        $templateProcessor->setValue('btz_name', $bank->name);
        $templateProcessor->setValue('count_questions', "11111111111111111111111111");
        $templateProcessor->setValue('time_testing', $validate['time_testing']);
        $templateProcessor->setValue('difficulty_level', $validate['difficulty_level']);
        $templateProcessor->setValue('max_score', $validate['max_score']);

        $templateProcessor->setValue('user_name', auth()->user()->first_name ." ". auth()->user()->last_name ." ". auth()->user()->patronymic);
        $templateProcessor->setValue('second_name', auth()->user()->last_name);
        $templateProcessor->setValue('first_name_reduction', substr(auth()->user()->first_name, 0, 1));
        $templateProcessor->setValue('patronymic_reduction', substr(auth()->user()->patronymic, 0, 1));

        $templateProcessor->setValue('now_date', date('d').$month[date('m')].date('Y'));

//        $templateProcessor->saveAs('C:/Users/vikto/Desktop/test/passport.docx');
//        https://phpword.readthedocs.io/en/latest/templates-processing.html#clonerowandsetvalues
    }


}
