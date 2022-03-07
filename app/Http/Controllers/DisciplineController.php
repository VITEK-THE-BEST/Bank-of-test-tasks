<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group Дисциплины
 *
 * редактирование дисциплин
 */
class DisciplineController extends Controller
{
    /**
     * Создание дисциплины
     *
     */
    public function create(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);
        $discipline = Discipline::query()->create($validate);
        return response()->json([$discipline]);
    }

    /**
     * Добавление пользователей в дисциплину
     *
     * @urlParam id дисциплина
     */
    public function addUsers(Request $request, Discipline $id)
    {
        $validate = $request->validate([
            'user_array' => 'required|array',
        ]);

        $users = User::query()->findOrFail($validate['user_array']);
        $id->users()->attach($users);

        return response()->json([]);
    }


    /**
     * Добавление банка в дисциплину
     *
     *
     * @urlParam discipline id
     * @urlParam bank id
     */
    public function addBank(Discipline $discipline,Bank $bank)
    {
        $discipline->banks()->syncWithoutDetaching([$bank->id]);
        return response()->json([]);
    }

    /**
     * Удаление дисциплины
     *
     * при удалении дисцпилины, так-же удаляется связь между банком и пользователями
     * @urlParam id дисциплина
     */
    public function delete(Discipline $id)
    {
        $id->delete();
        return response()->json([]);
    }

    /**
     * Обновление дисциплины
     *
     *
     * @urlParam id дисциплина
     */
    public function update(Request $request, Discipline $id)
    {
        $validate = $request->validate([
            "name" => "required",
        ]);
        $id->update($validate);
        return response()->json([]);
    }

    /**
     * !Дисциплины
     *
     * отобразить дисциплины пользователя
     *
     * @urlParam id дисциплина
     */
    public function show()
    {
        $user = User::query()->find(auth()->id());
        $disciplines = $user->disciplines();

        return response()->json([$disciplines]);
    }
}
