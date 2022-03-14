<?php

namespace App;

use App\Custom\Period;
use App\Custom\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Score
 *
 * @property int $id
 * @property int $student_id
 * @property int $lesson_id
 * @property int $schedule_id
 * @property string $date
 * @property int $lesson_num
 * @property int|null $index
 * @property string|null $value
 * @property int|null $type_id
 * @property string|null $comment
 * @property string $tms
 * @property Lesson $lesson
 * @property Schedule $schedule
 * @property User $student
 * @property ScoreType $type
 *
 * @method static Builder|Score query()
 * @method Builder|Score count()
 * @method static Builder|Score whereComment($value)
 * @method static Builder|Score whereDate($value)
 * @method static Builder|Score whereId($value)
 * @method static Builder|Score whereIndex($value)
 * @method static Builder|Score whereLessonId($value)
 * @method static Builder|Score whereLessonNum($value)
 * @method static Builder|Score whereScheduleId($value)
 * @method static Builder|Score whereStudentId($value)
 * @method static Builder|Score whereTms($value)
 * @method static Builder|Score whereTypeId($value)
 * @method static Builder|Score whereValue($value)
 */
class Score extends Model
{
    protected $table = 'score';

    public const ALL_SCORES = ['.', '5', '4', '3', '2', '1', 'ЗАЧЕТ', 'НЕ ЗАЧЕТ' ];
    public const POINT = '.';
    public const BAD_SCORE = 2;

    public static $scores = [];

    public function type() {
        return $this->hasOne('App\ScoreType', 'id', 'type_id');
    }

	public function student() {
		return $this->hasOne('App\User', 'id', 'student_id');
	}

	public function lesson() {
		return $this->hasOne('App\Lesson', 'id', 'lesson_id');
	}

	public function schedule() {
		return $this->hasOne('App\Schedule', 'id', 'schedule_id');
	}

	/**
	 * складывает оценки и весы
	 * @param Score $currScore
	 * @param int $studentId
	 * @param int $period
	 * @return void
	 */
    public static function fillScores($currScore, $studentId, $period): void {

        if($currScore->value === '.') {
            $value = 2;
        }

        else {
            $value = $currScore->value;
        }

        if(is_numeric($value)) {

            if (isset(self::$scores[$studentId][$period]['weighted'])) {
                self::$scores[$studentId][$period]['weighted']['dividend'] += $value * $currScore->type->weight;
                self::$scores[$studentId][$period]['weighted']['divisor'] += $currScore->type->weight;
            } else {
                self::$scores[$studentId][$period]['weighted']['dividend'] = $value * $currScore->type->weight;
                self::$scores[$studentId][$period]['weighted']['divisor'] = $currScore->type->weight;
            }
        }
	}

	/**
	 * считает итоги средневзвешенная и итоговая оценка
	 * @param int $studentId
	 * @param int $grade
	 * @return void
	 */
	public static function fillTotalScore($studentId, $grade): void {
    	$total = 0;
		$period = Period::definePeriod(time(), $grade);
		if (isset(self::$scores[$studentId])) {
			foreach (self::$scores[$studentId] as $k => $s) {
				if ($k <= $period) {
					self::$scores[$studentId][$k]['weighted']['total'] = number_format($s['weighted']['dividend'] / $s['weighted']['divisor'], 2);
					$total += self::$scores[$studentId][$k]['weighted']['total'];
				}
			}
			self::$scores[$studentId]['total'] = $total / Period::definePeriod(time(), $grade);
		}
	}

	public static function getRange(Request $request, $gradeId = null) {

	    if($request->get('schedule_id')) {
            $gradeNumber = Schedule::find($request->get('schedule_id'))->grade->number;
        }
	    else {
            $gradeNumber = Grade::find($gradeId)->number ?? 1;
        }

	    $currenteriod = Holiday::query()
            ->where('year', Year::getInstance()->getYear())
            ->where('period_type', $gradeNumber >= 10 ? 3 : 2 )
            ->where('end', '>=', Carbon::now()->toDateString())
            ->orderBy('begin', 'asc')
            ->first();

		return $request->get('date', [
			Carbon::parse($currenteriod->begin ?? Year::getInstance()->getYearBegin())->format('d.m.Y'),
			Carbon::parse($currenteriod->end ?? Year::getInstance()->getYearEnd())->format('d.m.Y')
		]);
	}

	public static function getHolidays(Request $request) {

		$dateRange = self::getRange($request);

		//we add two week so we can find all holidays even if end data is less then end data for holiday
		#foreach ($dateRange as $k => $d) {
		#	$dateRange[$k] = Carbon::parse($d)->addWeeks(2)->format('Y-m-d');
		#}

		$holidays = Holiday::getHolidays(Year::getInstance()->getYear(), $dateRange);

		$holidaysArray = [];
		foreach ($holidays as $h) {
			$holidaysArray[] = [Carbon::parse($h->begin)->timestamp, Carbon::parse($h->end)->timestamp];
		}

		return $holidaysArray;
	}

	public static function setHolidaysInDates($dates, $holidaysArray) {
		$number = $dates[0]['number'] ?? 1;
		foreach ($dates as $k => &$d) {

            $d['number'] = $number;
            $is_workday = true;
			foreach ($holidaysArray as $khd => $hd) {
				if ($d['date']->timestamp >= $hd[0] && $d['date']->timestamp <= $hd[1]) {
                    $d = false;
                    $is_workday = false;
                    break;
                }
			}
			if ($is_workday) {
                $number++;
            }
		}
		return $dates;
	}

	/**
	 * создаем фильтр
	 * @param Request $request
	 * @return array
	 */
	public static function createFilter(Request $request): array {
		return [
			'date' => [
				'title' => 'Дата',
				'type' => 'date-range',
				'value' => self::getRange($request)
			],
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Grade::getActive(),
				'value' => $request->get('grade_id'),
				'name_field' => 'number',
                'value_field' => 'id'
			],
/*            'grade_letter' => [
                'title' => 'Класс',
                'type' => 'select',
                'options' => Schedule::query()
                    ->where('grade_id', $request->get('grade_id'))
                    ->groupBy('grade_letter')
                    ->get(),
                'value' => $request->get('grade_letter'),
                'name_field' => 'grade_letter',
                'value_field' => 'grade_letter'
            ],

            'group_id' => [
                'title' => 'Группа',
                'type' => 'select',
                'options' => StudentGroup::query()
                    ->where('grade_id', $request->get('grade_id'))
                    ->where('lesson_id', $request->get('lesson_id'))
                    ->get(),
                'value' => $request->get('group_id'),
                'name_field' => 'name',
                'value_field' => 'id'
            ],*/
			'lesson_id' => [
				'title' => 'Предмет',
				'type' => 'select',
				'options' => Schedule::getLessonsByGradeId($request->get('grade_id')),
				'value' => $request->get('lesson_id'),
				'name_field' => 'name',
                'value_field' => 'id'
			],
			'teacher_id' => [
				'title' => 'Учитель',
				'type' => 'select',
				'options' => Schedule::getTeachersByGradeIdAndLessonId($request->get('grade_id'), $request->get('lesson_id')),
				'value' => $request->get('teacher_id'),
				'name_field' => 'name',
                'value_field' => 'id'
			],
			'letter_group_id' => [
				'title' => 'Класс-Группа',
				'type' => 'select',
				'options' => Schedule::createOptionsArray(
					Schedule::getLettersGroupsByGradeIdAndLessonIdAndTeacherId($request->get('grade_id'), $request->get('lesson_id'), $request->get('teacher_id')),
					true
				),
				'value' => $request->get('letter_group_id')
			],
		];
	}

	public static function createFilterMyScore(Request $request, $students, $student, $obStudent): array {
		return [
			'student_id' => [
				'title' => 'Ученик',
				'type' => 'select',
				'options' => $students,
				'value' => $student,
				'value_field' => 'id',
				'name_field' => 'name',
				'name' => 'student'
			],
			'period' => [
				'title' => 'Период',
				'type' => 'select',
				'options' => Period::$periodNames,
				'value' => $request->get('period', Period::defineKeyByGrade($obStudent->grade->number))
			],
			'date' => [
				'hide' => true,
				'value' => [
					Carbon::parse(Period::firstDayOfPeriod($obStudent->grade->number))->format('d.m.Y'),
					Carbon::parse(Period::lastDayOfPeriod($obStudent->grade->number))->format('d.m.Y')
				]
			]
		];
	}

	public static function allStudentsByRole($role) {
		return User::query()
			->select('users.*')
			->leftJoin('grade', 'grade.id', '=', 'users.class')
			->when($role === 'student', function ($query) {
				/** @var Builder $query */
				$query->where('users.id', Auth::user()->id);
			})
			->when($role === 'parent', function ($query) {
				/** @var Builder $query */
				$query->whereIn('users.id', StudentParent::getStudentsId());
			})
			->where('role_id', User::STUDENT)
			->orderBy('grade.year', 'desc')
			->orderBy('users.name', 'asc')
			->get();
	}

	public static function studentSchedule($obStudent, $year=null) {

        if (!$year) {
            $year = Year::getInstance()->getYear();
        }
		return Schedule::query()
			->select(
				'schedule.*',
				'lesson.name'
			)
			->leftJoin('grade', 'schedule.grade_id', '=', 'grade.id')
			->leftJoin('student_group_students', 'student_group_students.group_id', '=', 'schedule.group_id' )
			->leftJoin('lesson', 'lesson.id', '=', 'schedule.lesson_id')
			->where('schedule.grade_id', $obStudent->class)
			->where('schedule.year', $year)
			->where(function($query) use ($obStudent) {
				/** @var Builder $query */
				$query
					->where('all_class', 1)
					->orWhere('grade_letter', $obStudent->class_letter)
					->orWhere('student_group_students.student_id', $obStudent->id);
			})
			->groupBy('lesson.id')
			->orderBy('lesson.name', 'asc')
			->get();
	}

	public static function scoresMyScore($obStudent, $filter) {
		return Score::query()
			->leftJoin('schedule', 'schedule.id', '=', 'score.schedule_id')
			->leftJoin('score_types', 'score_types.id', '=', 'score.type_id')
			->where('score.student_id', $obStudent->id)
			->when($filter['period']['value'], function ($query) use ($filter) {
				/** @var Builder $query */
				$query->whereBetween('date', Period::defineFirstAndLastDays($filter));
			})
			->orderBy('date', 'asc')
			->get()
			->groupBy('schedule.lesson_id');
	}


	/**
	 * @param $studentId
	 * @param int $period
	 * @return Builder|\App\ScorePeriod
	 */
	public static function scorePeriodByPeriod($studentId, $period)
	{
		return ScorePeriod::query()
			->where('student_id', $studentId)
//			->whereBetween('date',
//					array_map(
//						function($e) {
//							return Carbon::parse($e)->format('Y-m-d');
//						},
//						[
//							Year::getInstance()->getYearBegin(),
//							Year::getInstance()->getYearEnd()
//						]
//					)
//				)
			->where('score_period.period_number', '=', $period)
            ->where('date', '>=', Carbon::parse(Year::getInstance()->getYearBegin()))
			->get()
			->keyBy('lesson_id');
	}

}
