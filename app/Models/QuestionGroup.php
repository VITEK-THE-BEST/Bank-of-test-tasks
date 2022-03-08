<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QuestionGroup
 * 
 * @property int $id
 * @property string $name
 * 
 * @property Collection|TypeQuestion[] $type_questions
 *
 * @package App\Models
 */
class QuestionGroup extends Model
{
	protected $table = 'question_groups';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function type_questions()
	{
		return $this->hasMany(TypeQuestion::class);
	}
}
