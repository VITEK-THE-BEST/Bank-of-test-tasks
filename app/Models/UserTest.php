<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserTest
 *
 * @property int $id
 * @property int $user_id
 * @property int $bank_id
 * @property float $assessment
 * @property array $result
 * @property Carbon $testing_time
 *
 * @property Bank $bank
 * @property User $user
 *
 * @package App\Models
 */
class UserTest extends Model
{
    use HasFactory;

    protected $table = 'user_tests';
    public $timestamps = false;

    protected $casts = [
        'user_id' => 'int',
        'bank_id' => 'int',
    ];

    protected $dates = [
        'time_testing',
        'start_testing',
        'end_testing'
    ];

    protected $fillable = [
        'user_id',
        'bank_id',
        'time_testing',
        'start_testing',
        'end_testing'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passed_tests()
    {
        return $this->hasMany(PassedTest::class);
    }
}
