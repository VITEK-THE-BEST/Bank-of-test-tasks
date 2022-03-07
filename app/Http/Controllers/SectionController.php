<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Section;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group Разделы
 *
 * разделы банка тестовых заданий
 */
class SectionController extends Controller
{
    /**
     * !Создание раздела
     * @urlParam Bank id
     */
    public function create(Request $request, Bank $bank)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);
        $validate['bank_id'] = $bank->id;

        Section::query()->create($validate);
        return response()->json([]);
    }

    /**
     * Разделы банка
     *
     * получить разделы банка
     *
     * @urlParam Bank id
     */
    public function show(Bank $bank)
    {
        return response()->json($bank->sections);
    }

    /**
     * Обновить название раздела
     *
     * то что закинул, то и обновится
     * @urlParam Section id
     */
    public function update(Request $request, Section $section)
    {
        $validate = $request->validate([
            'name' => 'sometimes|string',
        ]);

        $section->update($validate);
        return response()->json([]);
    }

    /**
     * !Удалить Раздел
     *
     * при удалении, ВРОДЕ КАК ДОЛЖНО ДРОПНУТЬСЯ И СВЯЗЬ С КАТЕГОРИЕЙ, НО НЕ САМА КАТЕГОРИЯ
     * @urlParam Section id
     */
    public function delete(Section $section)
    {
        $section->delete();
        return response()->json([]);
    }

    /**
     * Добавить к разделу категорию
     *
     * Связь многие ко многим
     * @urlParam Section id
     * @urlParam Category id
     */
    public function createCategory(Section $section, Category $category)
    {
        $section->categories()->syncWithoutDetaching($category);

        return response()->json([]);
    }

    /**
     * Удалить категорию из раздела
     *
     * Связь многие ко многим
     * @urlParam Section id
     * @urlParam Category id
     */
    public function deleteCategory(Section $section, Category $category)
    {
        $section->categories()->detach($category);

        return response()->json([]);
    }

    /**
     * !!!!!показать все категории которые не относятся к разделу и в соседних разделах пользователя
     *
     * !НЕ РАБОТАЕТ !
     */
    public function showNotCategory(Request $request)
    {
//        $categories = Category::all()->where('user_id', '=', auth()->id());
//
//        $btz = btz::find($request['btz_id']);
//        $sections_btz = $btz->sections;
//
//        foreach ($sections_btz as $item) {
//            $section = Section::find($item['id']);
//            $section_category = $section->categories;//это категории которые относятся к текущему разделу
//
//            foreach ($section_category as $section) {
//                $categories = $categories->filter(function ($category) use ($section) {
//                    if ($section['pivot']['category_id'] != $category['id']) {
//                        return $category;
//                    }
//                });
//            }
//        }
//
//        return response()->json(["success" => true,
//            "categories" => $categories->flatten()]);
    }

    /**
     * показать категории раздела
     *
     * @urlParam section id
     */
    public function showCategory(Section $section)
    {
        return response()->json($section->categories()->get());
    }

}
