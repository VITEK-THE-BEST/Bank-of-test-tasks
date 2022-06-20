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
    private function replace_symbol($correct_str): array|string
    {
        $symbols_replace = ["\\", "{", "}"];
        foreach ($symbols_replace as $symbol) {
            $correct_str = str_replace($symbol, '\\' . $symbol, $correct_str);
        }
        return $correct_str;
    }

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

                    $question['question'] = $this->replace_symbol($question['question']);

                    switch ($question['type_question_id']) {

                        case 1://открытый вопрос (один правильный ответ)
                            array_push($giftFile, $question['question'] . '{' . PHP_EOL);

                            foreach ($question['opinions'] as $opinion) {
                                if ($opinion['id'] == $question['answer'][0]) {
                                    array_push($giftFile, "=" . $this->replace_symbol($opinion['opinion']) . PHP_EOL);
                                } else {
                                    array_push($giftFile, "~" . $this->replace_symbol($opinion['opinion']) . PHP_EOL);
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
                                $answer_val = $answer_val . "=%100%" . $this->replace_symbol($answer) . "# ";
                            }
                            $question['question'] = str_replace("@@", "{ " . $answer_val . " }", $question['question']);

                            array_push($giftFile, $question['question'] . PHP_EOL . PHP_EOL);
                            break;
                        case 3: // на соответсвие
                            array_push($giftFile, $question['question'] . '{' . PHP_EOL);
                            foreach ($question['answer'] as $answer) {
                                foreach ($question['opinions'][0]['opinions'] as $opinion_opinion) {
                                    if ($opinion_opinion['id'] == $answer['id_opinion']) {
                                        $question_opinion = $this->replace_symbol($opinion_opinion['opinion']);
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
                                        array_push($giftFile, $this->replace_symbol($opinion['opinion']) . PHP_EOL);
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
        }, 'test.txt', $headers);
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

    /**
     * Выргузка паспорта
     *
     * 'scope_btz' => 'required',//входящая диагностика , текущий контроль ,промежуточный контроль (зачет), промежуточный контроль (экзамен), контроль остаточных знаний
     * 'time_testing' => 'required',//время провдение тестирования в минутах
     * 'difficulty_level' => 'required',//(базовый, повышенный, высокий)
     * 'max_score' => 'required',//максимальная оценка
     *
     *
     * ФОРМАТ ФАЙЛА DOCX
     * */
    public function passport(Request $request, Bank $bank)
    {
        $validate = $request->validate([
            'scope_btz' => 'required',//входящая диагностика , текущий контроль ,промежуточный контроль (зачет), промежуточный контроль (экзамен), контроль остаточных знаний
            'time_testing' => 'required',//время провдение тестирования в минутах
            'difficulty_level' => 'required',//(базовый, повышенный, высокий)
            'max_score' => 'required',//максимальная оценка
        ]);

        $month = ['01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' => 'Апреля', '05' => 'Май', '06' => 'Июнь', '07' => 'Июль', '08' => 'Август', '09' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь'];
        $templateProcessor = new TemplateProcessor('../storage/resourse/passport_template_new.docx');

        $bank_help = $bank->with(
            "sections"
        )->find($bank->id);

        $bank_help['sections'] = $bank_help['sections']->map(function ($section) {
            $section['count_questions'] = $section->categories()->get()
                ->map(function ($category) {
                    return $category->questions()->get()->count();
                })->sum();
            return $section;
        });


        $templateProcessor->setValue('scope_btz', $validate['scope_btz']);
        $templateProcessor->setValue('btz_name', $bank->name);
        $templateProcessor->setValue('credits', $bank->credits);
        $templateProcessor->setValue('count_questions', $bank_help['sections']->pluck('count_questions')->sum());
        $templateProcessor->setValue('time_testing', $validate['time_testing']);
        $templateProcessor->setValue('difficulty_level', $validate['difficulty_level']);
        $templateProcessor->setValue('max_score', $validate['max_score']);

        $templateProcessor->setValue('user_name', auth()->user()->first_name . " " . auth()->user()->last_name . " " . auth()->user()->patronymic);
        $templateProcessor->setValue('second_name', auth()->user()->last_name);
        $templateProcessor->setValue('first_name_reduction', substr(auth()->user()->first_name, 0, 1));
        $templateProcessor->setValue('patronymic_reduction', substr(auth()->user()->patronymic, 0, 1));

        $templateProcessor->setValue('now_date', date('d') . $month[date('m')] . date('Y'));

        ##заполнение таблицы
        $templateProcessor->cloneRow('section', $bank->sections->count());

        foreach ($bank->sections as $key => $val) {
            $key++;
            $section = Section::query()->with('categories')->find($val->id);
            $templateProcessor->cloneRow('category#' . $key, $section->categories->count());

            foreach ($section->categories as $i => $category) {
                $i++;
                $section_val = '';
                $section_num = '';
                if ($i == 1) {
                    $section_val = $section['name'];
                    $section_num = $key . '.';
                }

                $questions_type = $category->with([
                    'questions.type_question.question_group'
                ])->find($category->id);

                $count_type_question = $questions_type->questions->pluck('type_question.question_group')->countBy('name')->toArray();
                $q_1 = array_key_exists('Открытых', $count_type_question) ? $count_type_question['Открытых'] : 0;
                $q_2 = array_key_exists('Закрытых', $count_type_question) ? $count_type_question['Закрытых'] : 0;
                $q_3 = array_key_exists('На соответствие', $count_type_question) ? $count_type_question['На соответствие'] : 0;
                $q_4 = array_key_exists('На упорядочивание', $count_type_question) ? $count_type_question['На упорядочивание'] : 0;

                $templateProcessor->setValues([
                    "section#" . $key . '#' . $i => $section_val,
                    "category#" . $key . '#' . $i => $category['name'],

                    "q_1#" . $key . '#' . $i => $q_1,
                    "q_2#" . $key . '#' . $i => $q_2,
                    "q_3#" . $key . '#' . $i => $q_3,
                    "q_4#" . $key . '#' . $i => $q_4,
                    "section_n#" . $key . '#' . $i => $section_num,
                ]);
            }
        }
        $bank_count_group_type = $bank['sections']->map(function ($section) {
            return $section->categories()->get()
                ->map(function ($category) {
                    return $category->questions()->get()
                        ->map(function ($question) {
                            return $question->type_question->question_group;
                        });
                });
        });
        $bank_count_group_type = $bank_count_group_type->flatten()->countBy('name')->toArray();
        $q_all_1 = array_key_exists('Открытых', $bank_count_group_type) ? $bank_count_group_type['Открытых'] : 0;
        $q_all_2 = array_key_exists('Закрытых', $bank_count_group_type) ? $bank_count_group_type['Закрытых'] : 0;
        $q_all_3 = array_key_exists('На соответствие', $bank_count_group_type) ? $bank_count_group_type['На соответствие'] : 0;
        $q_all_4 = array_key_exists('На упорядочивание', $bank_count_group_type) ? $bank_count_group_type['На упорядочивание'] : 0;

        $templateProcessor->setValues([
            "q_all_1" => $q_all_1,
            "q_all_2" => $q_all_2,
            "q_all_3" => $q_all_3,
            "q_all_4" => $q_all_4,
        ]);

        $templateProcessor->saveAs('../storage/resourse/passport.docx');
        return response()->download('../storage/resourse/passport.docx');
    }


}
