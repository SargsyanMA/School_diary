<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lesson;
use Schedule;
use User;

/**
 * SQL for creating table
 * create table schedule_comment
 * (
 * id int auto_increment,
 * student_id int not null,
 * lesson_id int not null,
 * schedule_id int not null,
 * lesson_num int not null,
 * comment text not null,
 * date date not null,
 * created datetime default CURRENT_TIMESTAMP not null,
 * constraint schedule_comment_pk
 * primary key (id)
 * )
 * comment 'Комменты с привязкой к уроке';
 *
 * create index schedule_comment_date_index
 * on schedule_comment (date);
 *
 * create index schedule_comment_lesson_id_index
 * on schedule_comment (lesson_id);
 *
 * create index schedule_comment_lesson_num_index
 * on schedule_comment (lesson_num);
 *
 * create index schedule_comment_schedule_id_index
 * on schedule_comment (schedule_id);
 *
 * create index schedule_comment_student_id_index
 * on schedule_comment (student_id);
 *
 *
 * App\ScheduleComment
 *
 * @property int $id
 * @property int $student_id
 * @property int $lesson_id
 * @property int $schedule_id
 * @property int $lesson_num
 * @property string $comment
 * @property string $date
 * @property string $created
 *
 * @property Lesson $lesson
 * @property Schedule $schedule
 * @property User $student
 *
 * @method static Builder|ScheduleComment query()
 * @method Builder|ScheduleComment count()
 * @method static Builder|ScheduleComment whereComment($value)
 * @method static Builder|ScheduleComment whereDate($value)
 * @method static Builder|ScheduleComment whereId($value)
 * @method static Builder|ScheduleComment whereIndex($value)
 * @method static Builder|ScheduleComment whereLessonId($value)
 * @method static Builder|ScheduleComment whereLessonNum($value)
 * @method static Builder|ScheduleComment whereScheduleId($value)
 * @method static Builder|ScheduleComment whereStudentId($value)
 * @method static Builder|ScheduleComment whereTms($value)
 * @method static Builder|ScheduleComment whereTypeId($value)
 * @method static Builder|ScheduleComment whereValue($value)
 */
class ScheduleComment extends Model
{
    protected $table = 'schedule_comment';

    public function student()
    {
        return $this->hasOne('App\User', 'id', 'student_id');
    }

    public function lesson()
    {
        return $this->hasOne('App\Lesson', 'id', 'lesson_id');
    }

    public function schedule()
    {
        return $this->hasOne('App\Schedule', 'id', 'schedule_id');
    }

    /**
     * заполняем модель
     * @param $schedule
     * @param $student
     * @param $date
     * @param $request
     * @return array
     */
    public static function fillModel($schedule, $student, $date, $request): array
    {
        return [
            'student_id' => $student->id,
            'lesson_id' => $schedule->lesson->id,
            'schedule_id' => $schedule->id,
            'lesson_num' => $schedule->number,
            'date' => $date,
            'comment' => $request->get('comment'),
            'created' => Carbon::now()->toDateTimeLocalString()
        ];
    }

    /**
     * создает массив комментов для журнала
     * @param $studentsId
     * @param $lessonId
     * @param $dateYmd
     * @return array
     */
    public static function commentsForJournal($studentsId, $lessonId, $dateYmd): array
    {
        $comments = self::query()
            ->whereIn('student_id', $studentsId)
            ->where('lesson_id', $lessonId)
            ->whereIn('date', $dateYmd)
            ->get();

        foreach ($comments as $c) {
            /** @var ScheduleComment $c */
            $journalComments[$c->student_id][$c->date][$c->lesson_num][] = $c;
        }

        return $journalComments ?? [];
    }

}
