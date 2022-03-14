<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LessonType
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LessonType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LessonType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LessonType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LessonType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LessonType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LessonType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LessonType extends Model
{
    protected $table = 'lesson_types';
}
