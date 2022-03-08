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

        Category::query()->create($validate);
        return response()->json([]);
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
    public function delete(Category $сategory)
    {
        $сategory->delete();
        return response()->json([]);
    }

    /**
     * !!!!!!!получение категорий с количеством группы вопросов внутри категории
     *
     * МЕТОД ВРОДЕ БЫ НЕ НУЖЕН!?!?!?! НИКИТА СПАСИ
     *
     * вроде как теперь нужен такой метод не на категорию а на раздел
     */
    public function show()
    {
//        $categories = Category::select(['id', 'name'])
//            ->where('user_id', auth()->id())
//            ->get();
//
//
//        foreach ($categories as $category) {
//            $questions = question::select('type_question_id')
//                ->where('category_id', $category['id'])
//                ->get();
//
//            $type_question = type_question::all();
//
//            $list_types = [];
//
//            foreach ($type_question as $item) {
//
//                array_push($list_types, [
//                    'id' => $item['id'],
//                    'name' => $item['name'],
//                    'count' => $questions->where('type_question_id', $item['id'])->count()]);
//            }
//
//            $category['total_count'] = count($questions);
//            $category['type_count'] = $list_types;
//        }
//
//        return response()->json(["success" => true,
//            "categories" => $categories]);
    }

}
