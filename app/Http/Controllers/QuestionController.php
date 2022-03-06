<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;


/**
 * @authenticated
 * @group Вопросы
 *
 * Создание, изменение вопросов
 */
class QuestionController extends Controller
{
    /**
     * !Создание вопроса
     * @urlParam Category id
     */
    public function create(Request $request, Category $id)
    {
        $validate = $request->validate([
            'type_question_id' => 'required',
            'question' => 'required',
            'answer' => 'sometimes',
            'opinions' => 'sometimes',
        ]);
        $validate['category_id'] = $id->id;

        Question::query()->create($validate);
        return response()->json([]);
    }

    /**
     * !Обновление вопроса
     * @urlParam Category id
     *
     * что отправишь то и обновится
     */
    public function update(Request $request, Question $id)
    {
        $validate = $request->validate([
            'type_question_id' => 'sometimes',
            'question' => 'sometimes',
            'answer' => 'sometimes',
            'opinions' => 'sometimes',
        ]);

        $id->update($validate);
        return response()->json([]);
    }

    /**
     * !Удалить вопрос
     * @urlParam Question id
     */
    public function delete(Question $id)
    {
        $id->delete();
        return response()->json([]);
    }

    /**
     * !получить вопросы по категории
     * @urlParam Category id
     */
    public function show(Category $id)
    {
        $questions = question::query()
            ->where('category_id', $id->id)
            ->get();

        return response()->json($questions);
    }

    /**
     * !!!!!!!таблица вопросов категории
     *
     * получить колличество вопросов и типов вопросов
     * @urlParam Category id
     *
     * НАГАВНИЛ ПИЗДЕЦ, НАДО БЛЯТЬ ПЕРЕДЕЛАТЬ ВСЕ НАХУЙ
     */
    public function count(Category $id)
    {

//        $questions = question::select('type_question_id')->where('category_id', $validated['category_id'])->get();
//        $type_question = type_question::all();
//
//        $list_types = [];
//
//        foreach ($type_question as $item) {
//            array_push($list_types, ['id' => $item['id'], 'name' => $item['name'], 'count' => $questions->where('type_question_id', $item['id'])->count()]);
//        }
//
//        return response()->json(["success" => true,
//            "total_count" => count($questions),
//            "type_count" => $list_types]);
    }

    /**
     * !получить 1 вопрос по id
     * @urlParam Question id
     */
    public function take(Question $id)
    {
        return response()->json($id);
    }

}
