<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Discipline
 *
 * @property int $id
 * @property string $name
 *
 * @property Collection|Bank[] $banks
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Discipline extends Model
{
	protected $table = 'disciplines';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function banks()
	{
        return $this->belongsToMany(Bank::class)
            ->withPivot('id');
	}

	public function users()
	{
		return $this->belongsToMany(User::class)
					->withPivot('id');
	}
}
