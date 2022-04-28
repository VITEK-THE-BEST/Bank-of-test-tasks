<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group !Тесты пользователя
 *
 * тесты пройденные студентом
 */
class UserTestController extends Controller
{
    /**
     * !Добавление результата тестирования
     *
     * assessment - это результат прохождения тестирования, должен быть значением float от 0.00 до 1.00
     */
    public function create(Request $request, Bank $id)
    {
        $validate = $request->validate([
            'testing_time' => 'required',
            'result' => 'required',
        ]);

        return response()->json([]);
    }

    /**
     * !Статистика тестирования
     *
     */
    public function statistic(Request $request, Bank $id)
    {
        $validate = $request->validate([
            'test_id' => 'required',
        ]);

        return response()->json([]);
    }

    /**
     * Доступные на прохождения тесты пользователя
     *
     */
    public function userTest()
    {

        return response()->json([]);
    }
}
