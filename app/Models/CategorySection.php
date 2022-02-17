<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CategorySection
 * 
 * @property int $id
 * @property int $section_id
 * @property int $category_id
 * 
 * @property Category $category
 * @property Section $section
 *
 * @package App\Models
 */
class CategorySection extends Model
{
	protected $table = 'category_section';
	public $timestamps = false;

	protected $casts = [
		'section_id' => 'int',
		'category_id' => 'int'
	];

	protected $fillable = [
		'section_id',
		'category_id'
	];

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function section()
	{
		return $this->belongsTo(Section::class);
	}
}
