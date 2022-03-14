<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StudentAchievement
 *
 * @property int $id
 * @property int $student_id
 * @property int $type_id
 * @property string|null $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentAchievement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentAchievement extends Model
{
    protected $table = 'student_achievements';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'student_id', 'text', 'type_id'
	];
}
