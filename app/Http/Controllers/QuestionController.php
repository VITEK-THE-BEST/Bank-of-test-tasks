<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\TypeQuestion;
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
     * Создание вопроса
     *
     * для определенной категории
     * @urlParam Category id
     */
    public function create(Request $request, Category $category)
    {
        $validate = $request->validate([
            'type_question_id' => 'required|integer',
            'question' => 'required',
            'answer' => 'sometimes|array',
            'opinions' => 'sometimes|array',
        ]);
        $validate['category_id'] = $category->id;

        $question = Question::query()->create($validate);
        return response()->json($question);
    }

    /**
     * Обновление вопроса
     *
     * @urlParam Question id
     *
     * что отправишь то и обновится
     */
    public function update(Request $request, Question $question)
    {
        $validate = $request->validate([
            'type_question_id' => 'sometimes',
            'question' => 'sometimes',
            'answer' => 'sometimes',
            'opinions' => 'sometimes',
        ]);

        $question->update($validate);
        return response()->json([]);
    }

    /**
     * Удалить вопрос
     * @urlParam Question id
     */
    public function delete(Question $question)
    {
        $question->delete();
        return response()->json([]);
    }

    /**
     * получить вопросы по категории
     * @urlParam Category id
     */
    public function show(Category $category)
    {
        $questions = question::query()
            ->where('category_id', $category->id)
            ->get();

        return response()->json($questions);
    }

    /**
     * таблица вопросов в категории
     *
     * получить колличество вопросов и типов вопросов
     * @urlParam Category id
     *
     */
    public function count(Category $category)
    {
        $categoryQuestions = $category->questions()->get();

        $countTypeQuestion = $categoryQuestions->countBy(function ($question) {
            $typeQroupQuestion = TypeQuestion::query()
                ->select(['id', 'question_group_id'])
                ->find($question['type_question_id']);

            return $typeQroupQuestion->question_group['name'];
        });

        $questionGroup = QuestionGroup::all()->pluck('name');

        //добавляет значение при остуствие типа вопроса
        foreach ($questionGroup as $item) {
            $check = false;
            foreach ($countTypeQuestion as $keyCount => $valueCount) {
                if ($keyCount == $item) {
                    $check = true;
                    break;
                }
            }
            if (!$check) {
                $countTypeQuestion[$item] = 0;
            }
        }

        return response()->json($countTypeQuestion);
    }

    /**
     * получить 1 вопрос по id
     * @urlParam Question id
     */
    public function take(Question $question)
    {
        return response()->json($question);
    }

}
