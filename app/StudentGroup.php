<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StudentGroup
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $grade_id
 * @property int|null $lesson_id
 * @property string|null $schedules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Grade $grade
 * @property-read \App\Lesson $lesson
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $students
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
class StudentGroup extends Model
{
    protected $table = 'student_groups';


    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade_id');
    }

    public function lesson()
    {
        return $this->hasOne('App\Lesson', 'id', 'lesson_id');
    }

    public function students() {
        return $this->belongsToMany('App\User', 'student_group_students', 'group_id', 'student_id', 'id');
    }
}
