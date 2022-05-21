<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Section
 *
 * @property int $id
 * @property int $bank_id
 * @property string $name
 *
 * @property Bank $bank
 * @property Collection|Category[] $categories
 *
 * @package App\Models
 */
class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';
	public $timestamps = false;

	protected $casts = [
		'bank_id' => 'int'
	];

	protected $fillable = [
		'bank_id',
		'name'
	];

	public function bank()
	{
		return $this->belongsTo(Bank::class);
	}

	public function categories()
	{
		return $this->belongsToMany(Category::class);
//					->withPivot('id');
	}
}
