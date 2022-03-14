<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;

/**
 * App\Attendance
 *
 * @property int $id
 * @property int $student_id
 * @property int $lesson_id
 * @property int $schedule_id
 * @property string $date
 * @property int $lesson_num
 * @property string|null $type
 * @property string $value
 * @property string|null $comment
 * @property string $tms
 * @method static Builder|Attendance query()
 * @method static Builder|Attendance whereComment($value)
 * @method static Builder|Attendance whereDate($value)
 * @method static Builder|Attendance whereId($value)
 * @method static Builder|Attendance whereLessonId($value)
 * @method static Builder|Attendance whereLessonNum($value)
 * @method static Builder|Attendance whereScheduleId($value)
 * @method static Builder|Attendance whereStudentId($value)
 * @method static Builder|Attendance whereTms($value)
 * @method static Builder|Attendance whereType($value)
 * @method static Builder|Attendance whereValue($value)
 */
class Attendance extends AttendanceSchool
{
    protected $table = 'attendance';

    public const MINUTES = [1,3,5,10,15,20,30];

    public function schedule() {
        return $this->hasOne('App\Schedule', 'id', 'schedule_id');
    }


}
