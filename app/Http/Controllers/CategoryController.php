<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
     * получение категорий с количествомвопросов
     *
     */
    public function show()
    {
        $user = auth()->user();

        $categories = $user
            ->categories()
            ->get()
            ->map(function ($category){
                $category['count_questions'] = $category->questions()->get()->count();
                return $category;
            });
        return response()->json($categories);
    }

}
