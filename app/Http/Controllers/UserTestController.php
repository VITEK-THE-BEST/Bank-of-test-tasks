<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group !Тесты пользователя !не работают!
 *
 * тесты пройденные студентом
 */
class UserTestController extends Controller
{
    /**
     * !Добавление результата тестирования
     *
     * assessment - это результат прохождения тестирования, должен быть значением float от 0.00 до 1.00
     * не работает, надо потом доработать прохождение тестирования
     */
    public function create(Request $request, Bank $id)
    {
        $validate = $request->validate([
            'testing_time' => 'required',
            'result' => 'required',
        ]);

        return response()->json(["метод блять не рабочий !!!!"]);
    }
}
