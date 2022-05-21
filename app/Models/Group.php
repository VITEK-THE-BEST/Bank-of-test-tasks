<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 *
 * @property int $id
 * @property string $name
 *
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
