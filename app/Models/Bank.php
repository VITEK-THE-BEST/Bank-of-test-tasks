<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Bank
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $discipline_id
 * @property string $name
 * @property Carbon $start_testing
 * @property Carbon $end_testing
 *
 * @property Discipline|null $discipline
 * @property User $user
 * @property Collection|Section[] $sections
 * @property Collection|UserTest[] $user_tests
 *
 * @package App\Models
 */
class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'discipline_id' => 'int'
	];

	protected $dates = [
		'start_testing',
		'end_testing'
	];

	protected $fillable = [
		'user_id',
		'discipline_id',
		'name',
		'credits',
		'start_testing',
		'end_testing'
	];

	public function disciplines()
	{
		return $this->belongsToMany(Discipline::class)
            ->withPivot('id');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function sections()
	{
		return $this->hasMany(Section::class);
	}

	public function user_tests()
	{
		return $this->hasMany(UserTest::class);
	}
}
