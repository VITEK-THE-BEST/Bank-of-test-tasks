<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\QuestionGroup;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group Категория
 *
 * Категории разделовф
 */
class CategoryController extends Controller
{
    /**
     * Создание категории
     */
    public function create(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);
        $validate['user_id'] = auth()->id();

        $category = Category::query()->create($validate);
        return response()->json($category);
    }

    /**
     * Обновить название категории
     *
     * @urlParam Category id
     */
    public function update(Request $request, Category $category)
    {
        $validate = $request->validate([
            'name' => 'sometimes|string',
        ]);

        $category->update($validate);
        return response()->json([]);
    }

    /**
     * !Удалить категорию
     *
     * при удалении, должно дропнуться и связь в таблице многие ко многим
     *
     * @urlParam Category id
     */
    public function delete(Category $category)
    {
        $category->delete();
        return response()->json([]);
    }

    /**
     * получение категорий пользователя с количеством вопросов
     *
     */
    public function show()
    {
        $user = auth()->user();

        $categories = $user
            ->categories()
            ->get()
            ->map(function ($category) {
                $category['count_questions'] = $category->questions()->get()->count();
                return $category;
            });
        return response()->json($categories);
    }

    /**
     * получение детальной информации категории
     *
     * получение колличества типов вопросов в категории
     */
    public function showDetail(Category $category)
    {
        $category_groups = $category
            ->questions()
            ->get()
            ->map(function ($question) {
                return $question->type_question()
                    ->get()
                    ->map(function ($type_question) {
                        return $type_question->question_group;
                    });
            });

        $category_table = [];
        $questionGroup = QuestionGroup::all();

        foreach ($questionGroup as $value) {
            $value_past = $category_groups->collapse()->where('id', $value['id'])->count();//подсчет колличества типа вопроса
            array_push($category_table, [$value['name'] => $value_past]);
        }
        $category['category_table'] = $category_table;

        return response()->json($category);
    }

}
