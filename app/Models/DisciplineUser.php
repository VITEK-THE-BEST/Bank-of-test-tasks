<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DisciplineUser
 * 
 * @property int $id
 * @property int $discipline_id
 * @property int $user_id
 * 
 * @property Discipline $discipline
 * @property User $user
 *
 * @package App\Models
 */
class DisciplineUser extends Model
{
	protected $table = 'discipline_user';
	public $timestamps = false;

	protected $casts = [
		'discipline_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'discipline_id',
		'user_id'
	];

	public function discipline()
	{
		return $this->belongsTo(Discipline::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
