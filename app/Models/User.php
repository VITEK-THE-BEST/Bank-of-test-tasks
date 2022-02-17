<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property int $group_id
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic
 * @property string $email
 * @property string $password
 * 
 * @property Group $group
 * @property Collection|Bank[] $banks
 * @property Collection|Discipline[] $disciplines
 * @property Collection|UserTest[] $user_tests
 *
 * @package App\Models
 */
class User extends Model
{
	protected $table = 'users';
	public $timestamps = false;

	protected $casts = [
		'group_id' => 'int'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'group_id',
		'first_name',
		'last_name',
		'patronymic',
		'email',
		'password'
	];

	public function group()
	{
		return $this->belongsTo(Group::class);
	}

	public function banks()
	{
		return $this->hasMany(Bank::class);
	}

	public function disciplines()
	{
		return $this->belongsToMany(Discipline::class)
					->withPivot('id');
	}

	public function user_tests()
	{
		return $this->hasMany(UserTest::class);
	}
}
