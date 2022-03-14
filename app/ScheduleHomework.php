<?php

namespace App;

use App\Custom\Period;
use Carbon\Carbon;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lesson;
use Schedule;
use User;

/**
 * SQL for creating table
 * create table schedule_homework
 * (
 * id int auto_increment,
 * student_id int not null,
 * lesson_id int not null,
 * schedule_id int not null,
 * lesson_num int not null,
 * is_homework bool default true not null,
 * date date not null,
 * created datetime default CURRENT_TIMESTAMP not null,
 * constraint schedule_homework_pk
 * primary key (id)
 * )
 * comment 'ДЗ с привязкой к уроке';
 * create index schedule_homework_date_index
 * on schedule_homework (date);
 * create index schedule_homework_lesson_id_index
 * on schedule_homework (lesson_id);
 * create index schedule_homework_lesson_num_index
 * on schedule_homework (lesson_num);
 * create index schedule_homework_schedule_id_index
 * on schedule_homework (schedule_id);
 * create index schedule_homework_student_id_index
 * on schedule_homework (student_id);
 *
 *
 * App\ScheduleHomework
 *
 * @property int $id
 * @property int $student_id
 * @property int $lesson_id
 * @property int $schedule_id
 * @property int $lesson_num
 * @property bool $is_homework
 * @property string $date
 * @property string $created
 *
 * @property Lesson $lesson
 * @property Schedule $schedule
 * @property User $student
 *
 * @method static Builder|ScheduleHomework query()
 * @method Builder|ScheduleHomework count()
 * @method static Builder|ScheduleHomework whereComment($value)
 * @method static Builder|ScheduleHomework whereDate($value)
 * @method static Builder|ScheduleHomework whereId($value)
 * @method static Builder|ScheduleHomework whereIndex($value)
 * @method static Builder|ScheduleHomework whereLessonId($value)
 * @method static Builder|ScheduleHomework whereLessonNum($value)
 * @method static Builder|ScheduleHomework whereScheduleId($value)
 * @method static Builder|ScheduleHomework whereStudentId($value)
 * @method static Builder|ScheduleHomework whereCreated($value)
 */
class ScheduleHomework extends Model
{
    protected $table = 'schedule_homework';

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
     * Заполняем модель
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
            'is_homework' => $request->get('is_homework'),
            'created' => Carbon::now()->toDateTimeLocalString()
        ];
    }

    /**
     * создает массив с ДЗ которые не сделанные для журнала
     * @param $studentsId
     * @param $lessonId
     * @param $dateYmd
     * @return array
     */
    public static function commentsForJournal($studentsId, $lessonId, $dateYmd): array
    {
        $homeworks = self::query()
            ->whereIn('student_id', $studentsId)
            ->where('lesson_id', $lessonId)
            ->where('is_homework', false)
            ->whereIn('date', $dateYmd)
            ->get();

        foreach ($homeworks as $h) {
            /** @var ScheduleComment $c */
            $journalHomeworks[$h->student_id][$h->date][$h->lesson_num] = $h;
        }

        return $journalHomeworks ?? [];
    }

    /**
     * Несделанные уроки по ученику и периоду (группируем по предмету)
     * @param $studentId
     * @param null $filter
     * @return array|BuildsQueries[]|Builder[]|Collection
     */
    public static function rowsByStudentAndPeriod($studentId, $filter = null)
    {
        return self::query()
            ->where(['student_id' => $studentId, 'is_homework' => false])
            ->when($filter['period']['value'], static function ($query) use ($filter) {
                /** @var Builder $query */
                $query->whereBetween('date', Period::defineFirstAndLastDays($filter));
            })
            ->get()
            ->groupBy(['lesson_id']);
    }
}
