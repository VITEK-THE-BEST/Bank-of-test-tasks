<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassedTest extends Model
{
    use HasFactory;
    protected $table = 'passed_tests';
    public $timestamps = false;

    protected $casts = [
        'user_test_id' => 'int',
        'assessment' => 'float',
        'result' => 'array'
    ];

    protected $dates = [
        'start_testing',
        'end_testing'
    ];

    protected $fillable = [
        'user_test_id',
        'assessment',
        'result',
        'start_testing',
        'end_testing'
    ];


    public function user_tests()
    {
        return $this->belongsTo(User::class);
    }
}
