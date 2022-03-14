<?php

namespace App;

use App\Custom\Period;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\AttendanceSchool
 *
 * QUERY
 *
 * create table attendance_school
 * (
 * id int auto_increment,
 * student_id int not null,
 * date date not null,
 * type varchar(10) not null,
 * minutes int(4) null,
 * grade_id int(4) not null,
 * comment text null,
 * tms timestamp default CURRENT_TIMESTAMP not null,
 * constraint attendance_school_pk
 * primary key (id)
 * );
 *
 * create index attendance_school_grade_id_index
 * on attendance_school (grade_id);
 *
 * create index attendance_school_date_index
 * on attendance_school (date);
 *
 * create index attendance_school_minutes_index
 * on attendance_school (minutes);
 *
 * create index attendance_school_student_id_index
 * on attendance_school (student_id);
 *
 * create index attendance_school_type_index
 * on attendance_school (type);
 *
 * create unique index attendance_school_date_student_id_uindex
 * on attendance_school (date, student_id);
 *
 * @property int $id
 * @property int $student_id
 * @property int $grade_id
 * @property string $date
 * @property string|null $type
 * @property integer|null $minutes
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
 * @method static Builder|Attendance firstOrNew($value)
 */
class AttendanceSchool extends Model
{
    protected $table = 'attendance_school';

    public const MINUTES = [1, 3, 5, 10, 15, 20, 30];

    protected $fillable = ['student_id', 'date'];

    //we dont use created_at and updated_at
    public $timestamps = false;

    public static function createFilter(Request $request): array
    {
        return [
            'grade_id' => [
                'title' => 'Параллель',
                'type' => 'select',
                'options' => Grade::getActive(),
                'value' => (int)$request->get('grade_id'),
                'name_field' => 'number',
                'value_field' => 'id'
            ],
            'period' => [
                'title' => 'Период',
                'type' => 'select',
                'options' => Period::$periodNames,
                'value' => $request->get('period')
            ]
        ];
    }

    /**
     * @param $date
     * @param $student
     * @param $request
     * @return array
     */
    public static function updateOrInsertAttendance($date, $student, $request): array
    {
        $model = self::firstOrNew([
            'student_id' => $student->id,
            'date' => $date->toDateString(),
        ]);

        $type = $request->get('type');
        $model->type = $type;
        $model->grade_id = $student->class;
        $model->minutes = 'absent' === $type ? null : $request->get('minutes');
        $model->comment = $request->get('comment');
        $model->tms = Carbon::now()->toDateTimeLocalString();

        return ['success' => $model->save(), 'id' => $model->id??null];
    }

    /**
     * @param $id
     * @param $studentId
     * @return Builder[]|Collection
     */
    public static function deleteByIdAndStudent($id, $studentId)
    {
        return self::query()
            ->where('id', $id)
            ->where('student_id', $studentId)
            ->delete();
    }

    /**
     * @param $gradeId
     * @param array $yearRange
     * @return Builder[]|Collection
     */
    public static function findByGrade($gradeId, array $yearRange)
    {
        return self::query()
            ->where('grade_id', $gradeId)
            ->whereBetween('date', [$yearRange[0]->format('Y-m-d'), '2020-09-01'/*$yearRange[1]->format('Y-m-d')*/])
            ->get();
    }

    /**
     * @param $gradeId
     * @param array $yearRange
     * @return array
     */
    public static function createAttendanceByDate($gradeId, array $yearRange): array
    {
        $res = [];
        $attendances = self::findByGrade($gradeId, $yearRange);

        foreach ($attendances as $attendance) {
            /** @var self $attendance */
                $res[$attendance->student_id][$attendance->date] = $attendance;
        }

        return $res;
    }

}
