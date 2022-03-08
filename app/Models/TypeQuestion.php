<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeQuestion
 * 
 * @property int $id
 * @property int $question_group_id
 * @property string $name
 * 
 * @property QuestionGroup $question_group
 * @property Collection|Question[] $questions
 *
 * @package App\Models
 */
class TypeQuestion extends Model
{
	protected $table = 'type_questions';
	public $timestamps = false;

	protected $casts = [
		'question_group_id' => 'int'
	];

	protected $fillable = [
		'question_group_id',
		'name'
	];

	public function question_group()
	{
		return $this->belongsTo(QuestionGroup::class);
	}

	public function questions()
	{
		return $this->hasMany(Question::class);
	}
}
