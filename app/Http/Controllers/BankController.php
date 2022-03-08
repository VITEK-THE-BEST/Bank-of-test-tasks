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
            'start_testing' => 'sometimes|date',
            'end_testing' => 'sometimes|date',
        ]);
        $validate['user_id'] = auth()->id();

        Bank::query()->create($validate);
        return response()->json([]);
    }

    /**
     * Банки пользователя
     *
     * получить банки авторизированного пользователя
     */
    public function show()
    {
        $banks = Bank::query()->where('user_id', auth()->id())->get();
        return response()->json($banks);
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
