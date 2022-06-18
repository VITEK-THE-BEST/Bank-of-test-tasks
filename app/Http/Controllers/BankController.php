<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group Банк
 *
 * редактирование банк
 */
class BankController extends Controller
{
    /**
     * Создание банка
     */
    public function create(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'credits' => 'required',
            'start_testing' => 'sometimes|date',
            'end_testing' => 'sometimes|date',
        ]);
        $validate['user_id'] = auth()->id();

        $bank = Bank::query()->create($validate);
        return response()->json($bank);
    }

    /**
     * Банки пользователя
     *
     * получить банки авторизированного пользователя c колличеством вопросов внутри банка
     */
    public function show()
    {
        $user = auth()->user();

        $banks = $user->banks
            ->map(function ($bank) {
                $bank['count_questions'] = $bank->sections()->get()
                    ->map(function ($section) {
                        return $section->categories()->get()
                            ->map(function ($category) {
                                return $category->questions()->get()->count();
                            })->sum();
                    })->sum();

                return $bank;
            });

        return response()->json($banks);
    }

    /**
     * Банки пользователя для  выгрузки
     *
     * получить банки авторизированного пользователя c колличеством типов вопросов по зачетным единицам
     *
     *
     * закрытой формы  – не более 70%;
     *
     * открытой формы  не менее 5%;
     *
     * установление соответствия  не менее 5%;
     *
     * на установление правильной последовательности  – на усмотрение разработчика БТЗ.
     *
     */
    public function showUnload()
    {
        $user = auth()->user();


        $banks = $user->banks
            ->map(function ($bank) {

                $minCountQuestions = match ($bank['credits']) {
                    2 => 100,
                    3 => 150,
                    4 => 200,
                    default => 250,
                };

                $bank['min_count_questions'] = $minCountQuestions;
                $questions_types = $bank->sections()->get()
                    ->map(function ($section) {
                        return $section->categories()->get()
                            ->map(function ($category) {
                                return $category->questions()->get()
                                    ->map(function ($question) {
                                        return $question->type_question()->get()
                                            ->map(function ($type_question) {
                                                return $type_question->question_group()->get();
                                            });
                                    });

                            });
                    });

                $questions_types = $questions_types->flatten()->countBy('id');
                //добавить пустое значение
                foreach (['1', '2', '3', '4'] as $value) {
                    if (!$questions_types->has($value)) {
                        $questions_types[$value] = 0;
                    }
                }

                $count_questions = $questions_types->sum();
                $bank['count_questions'] = $count_questions;

                $question_table = [];

                //открытых не менее 5%
                $min = ($count_questions * 5) / 100;
                $result = ($questions_types['1'] * 100) / $min;

                array_push($question_table, [
                    "name" => "Открытых",
                    "min" => 5,
                    'result' => $result
                ]);

                //закрытой формы не более 70%
                $min = ($count_questions * 70) / 100;
                $result = ($questions_types['2'] * 100) / $min;

                array_push($question_table, [
                    "name" => "Закрытых",
                    "max" => 70,
                    'result' => $result
                ]);

                //соответствие не менее 5%
                $min = ($count_questions * 5) / 100;
                $result = ($questions_types['3'] * 100) / $min;

                array_push($question_table, [
                    "name" => "На соответсвие",
                    "min" => 5,
                    'result' => $result
                ]);

                //упорядочивание на усмотрение разработчика БТЗ
                array_push($question_table, [
                    "name" => "На упрорядочивание",
                    "min" => 0,
                    'result' => ($questions_types['3'] * 100) / $count_questions
                ]);

                $bank['question_table'] = $question_table;

                return $bank;
            });

        return response()->json($banks);
    }

    /**
     * Получить банк по id
     *
     * получить детальную информацию о банке
     */
    public function showDetails(Bank $bank)
    {

        $bank = $bank->with(
            "sections"
        )->find($bank->id);

        $bank['sections'] = $bank['sections']->map(function ($section) {
            $section['count_questions'] = $section->categories()->get()
                ->map(function ($category) {
                    return $category->questions()->get()->count();
                })->sum();
            return $section;
        });

        $bank['count_questions'] = $bank['sections']->pluck('count_questions')->sum();

        return response()->json($bank);
    }

    /**
     * Обновить данные банка
     *
     * то что закинул, то и обновится
     * @urlParam Bank id
     */
    public function update(Request $request, Bank $id)
    {
        $validate = $request->validate([
            'name' => 'sometimes|string',
            'credits' => 'sometimes|string',
            'start_testing' => 'sometimes|date',
            'end_testing' => 'sometimes|date',
        ]);

        $id->update($validate);
        return response()->json([]);
    }

    /**
     * !!!!!!Удалить банк
     *
     * при удалении, дропнуться и разделы и связи с дисциплинной
     *
     * @urlParam Bank id
     */
    public function delete(Bank $id)
    {
        $id->delete();
        return response()->json([]);
    }
}
