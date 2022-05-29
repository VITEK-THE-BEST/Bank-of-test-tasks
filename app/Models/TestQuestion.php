<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory;

    protected $table = 'test_questions';
    public $timestamps = false;

    protected $casts = [
        'user_test_id' => 'int',
        'type_question_id' => 'int',
        'question' => 'string',
        'answer' => 'json',
        'opinions' => 'json',
    ];

    protected $fillable = [
        'user_test_id',
        'type_question_id',
        'question',
        'answer',
        'opinions',
    ];

    public function user_test()
    {
        return $this->belongsTo(UserTest::class);
    }

    public function type_question()
    {
        return $this->belongsTo(TypeQuestion::class);
    }
}
