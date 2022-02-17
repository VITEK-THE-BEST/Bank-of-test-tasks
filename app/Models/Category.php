<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * 
 * @property int $id
 * @property string $name
 * 
 * @property Collection|Section[] $sections
 * @property Collection|Question[] $questions
 *
 * @package App\Models
 */
class Category extends Model
{
	protected $table = 'categories';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function sections()
	{
		return $this->belongsToMany(Section::class)
					->withPivot('id');
	}

	public function questions()
	{
		return $this->hasMany(Question::class);
	}
}
