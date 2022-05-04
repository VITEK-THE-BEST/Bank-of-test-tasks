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

        $banks = $user->banks()->get()
            ->map(function ($bank) {
                $bank['count_questions'] = $bank->sections()->get()
                    ->map(function ($section) {
                        return $section->categories()->get()
                            ->map(function ($category) {
                                return $category->questions()->get();
                            });
                    })
                    ->count();

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
        )->get()->find($bank->id);

        $bank['sections'] = $bank['sections']->map(function ($section) {
            $count_question = $section
                ->categories()
                ->get()
                ->map(function ($category) {
                    return $category->questions;
                })->count();

            $section['count_questions'] = $count_question;
            return $section;
        });
        $count_questions = 0;
        foreach ($bank['sections']->pluck('count_questions') as $count_question) {
            $count_questions += $count_question;
        }
        $bank['count_questions'] = $count_questions;

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
