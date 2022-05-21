<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 *
 * @property Collection|Section[] $sections
 * @property Collection|Question[] $questions
 *
 * @package App\Models
 */
class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    public $timestamps = false;

    protected $casts = [
        'user_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function sections()
    {
        return $this->belongsToMany(Section::class)
            ->withPivot('id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
