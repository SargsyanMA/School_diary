<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Lesson
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $type_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\LessonType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lesson whereTypeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lesson whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lesson extends Model
{
    protected $table = 'lesson';

    public function type()
    {
        return $this->hasOne('App\LessonType', 'code', 'type_code');
    }

    /**
     * Если админ то возвращаем все предметы, если преподаватель то, только его предметы
     * @return Builder[]|Collection
     */
    public static function lessonsForRole()
    {
        $lessons = self::query()
            ->select('lesson.*')
            ->join('schedule', 'schedule.lesson_id', '=', 'lesson.id')
            ->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
            ->groupBy('lesson.id')
            ->orderBy('lesson.name');

        if (Auth::user()->role->name == 'admin') {
            return $lessons->get();
        }

        return $lessons->where('st.teacher_id', Auth::user()->id)->get();
    }
}
