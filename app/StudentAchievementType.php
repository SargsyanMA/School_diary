<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StudentAchievementType
 *
 * @property int $id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievementType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievementType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievementType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievementType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievementType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentAchievementType extends Model
{
    protected $table = 'student_achievements_type';
}
