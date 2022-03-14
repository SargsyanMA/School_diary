<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StudentGroupStudent
 *
 * @property int $group_id
 * @property int $student_id
 * @property string $date_from
 * @property string $date_to
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroupStudent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroupStudent whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroupStudent whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroupStudent whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentGroupStudent whereStudentId($value)
 * @mixin \Eloquent
 */
class StudentGroupStudent extends Model
{
}
