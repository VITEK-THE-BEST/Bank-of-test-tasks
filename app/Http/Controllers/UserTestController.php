<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\TestQuestion;
use App\Models\UserTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @authenticated
 * @group Управления тестирвоаниями
 *
 * управление тестированиями, добавление удаление и тд
 */
class UserTestController extends Controller
{
    /**
     * Отправить банк на тестирование
     *
     * ВНИМАНИЕ ВОПРОСЫ ИЗ БАНКА ДОБАВЯТСЯ В ОТДЕЛЬНУЮ ТАБЛИЦУ И ИЗМЕНИТЬ ИХ УЖЕ НЕ БУДЕТ ВОЗМОЖНОСТИ ТОЛЬКО УДАЛИТЬ ТЕСТ
     *
     */
    public function create(Request $request, Bank $bank)
    {
        $bank->
        $validate = $request->validate([
            'name' => 'required',
            'start_testing' => 'required',
            'end_testing' => 'required',
        ]);

        $validate['bank_id'] = $bank->id;
        $validate['user_id'] = auth()->id();

        $test = UserTest::query()->create($validate);

        $query_questions = [];

        $bank = Bank::query()
            ->with(['sections.categories.questions'])
            ->find($bank->id);

        $questions = $bank['sections']
            ->pluck('categories.*.questions')
            ->flatten();

        foreach ($questions as $question) {
            array_push($query_questions, new TestQuestion([
                'type_question_id' => $question['type_question_id'],
                'category_id' => $test['id'],
                'question' => $question['question'],
                'answer' => $question['answer'],
                'opinions' => $question['opinions']
            ]));
        }
        $test->test_questions()->saveMany($query_questions);
        $test['count_questions'] = count($query_questions);
        return response()->json($test);
    }

    /**
     * показать все активные тесты пользователя
     *
     */
    public function show()
    {
        $tests = auth()->user()
            ->user_tests()
            ->get();

        return response()->json($tests);
    }

    /**
     * обновить время тестирования
     *
     */
    public function update(Request $request, UserTest $userTest)
    {
        $validate = $request->validate([
            'time_testing' => 'sometimes',
            'start_testing' => 'sometimes',
            'end_testing' => 'sometimes',
        ]);
        $userTest->update($validate);

        return response()->json([]);
    }

    /**
     * удалить тест
     *
     */
    public function delete(UserTest $userTest)
    {
        $userTest->delete();
        return response()->json([]);
    }

}
