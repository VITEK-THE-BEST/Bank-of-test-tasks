<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

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
