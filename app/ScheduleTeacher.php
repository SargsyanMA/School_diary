<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ScheduleTeacher
 *
 * @property int $schedule_id
 * @property int $teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereSchedules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScheduleTeacher extends Model
{
    protected $table = 'schedule_teachers';

	public function teacher()
	{
		return $this->hasOne('App\User', 'id', 'teacher_id');
	}

	public static function setTeachersForSchedule($teachers, $id) {
		if (is_array($teachers)) {
			$data = [];
			foreach ($teachers as $t) {
				$data[] = [
					'schedule_id' => $id,
					'teacher_id' => $t
				];
			}
			ScheduleTeacher::insert($data);
		}
	}
}
