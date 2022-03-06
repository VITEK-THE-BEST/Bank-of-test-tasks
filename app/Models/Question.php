<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Question
 *
 * @property int $id
 * @property int $category_id
 * @property int $type_question_id
 *
 * @property Category $category
 * @property TypeQuestion $type_question
 *
 * @package App\Models
 */
class Question extends Model
{
	protected $table = 'questions';
	public $timestamps = false;

	protected $casts = [
		'category_id' => 'int',
        'type_question_id' => 'int',
		'question' => 'string',
		'answer' => 'json',
		'opinions' => 'json',
	];

	protected $fillable = [
		'category_id',
		'type_question_id',
        'question',
        'answer',
        'opinions',
	];

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function type_question()
	{
		return $this->belongsTo(TypeQuestion::class);
	}
}
