<?php

namespace App;

use App\Custom\Year;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Builder;

/**
 * App\Schedule
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $grade_id
 * @property string|null $grade_letter
 * @property int $weekday
 * @property int $number
 * @property string|null $time_begin
 * @property string|null $time_end
 * @property int|null $teacher_id
 * @property string|null $student_id
 * @property int $all_class
 * @property string|null $type
 * @property string|null $note
 * @property string $tms
 * @property string $tms_end
 * @property int $year
 * @property int|null $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read mixed $students
 * @property-read Grade $grade
 * @property-read ScheduleTeacher $scheduleTeacher
 * @property-read StudentGroup $group
 * @property-read Lesson $lesson
 * @property-read User $teacher
 *
 * @method static Builder|Schedule query()
 * @method static Builder|Schedule whereAllClass($value)
 * @method static Builder|Schedule whereCreatedAt($value)
 * @method static Builder|Schedule whereGradeId($value)
 * @method static Builder|Schedule whereGradeLetter($value)
 * @method static Builder|Schedule whereGroupId($value)
 * @method static Builder|Schedule whereId($value)
 * @method static Builder|Schedule whereLessonId($value)
 * @method static Builder|Schedule whereNote($value)
 * @method static Builder|Schedule whereNumber($value)
 * @method static Builder|Schedule whereStudentId($value)
 * @method static Builder|Schedule whereTeacherId($value)
 * @method static Builder|Schedule whereTimeBegin($value)
 * @method static Builder|Schedule whereTimeEnd($value)
 * @method static Builder|Schedule whereTms($value)
 * @method static Builder|Schedule whereTmsEnd($value)
 * @method static Builder|Schedule whereType($value)
 * @method static Builder|Schedule whereUpdatedAt($value)
 * @method static Builder|Schedule whereWeekday($value)
 * @method static Builder|Schedule whereYear($value)
 */
class Schedule extends Model
{
	public $homeworks = [];
	public $plans = [];
	public $stud = [];
	public $attendance = [];
	public $scores = [];
	public $scoresPeriod = [];
	public $comments = [];
	public $isHomeworks = [];

    protected $table = 'schedule';

    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade_id');
    }

    public function lesson()
    {
        return $this->hasOne('App\Lesson', 'id', 'lesson_id');
    }

    public function teacher()
    {
        return $this->hasOne('App\User', 'id', 'teacher_id');
    }

	public function scheduleTeacher()
	{
		return $this->hasMany('App\ScheduleTeacher', 'schedule_id', 'id');
	}

    public function group()
    {
        return $this->hasOne('App\StudentGroup', 'id', 'group_id');
    }

    public function getActiveAttribute() {
        return Carbon::parse($this->tms_end) >= Carbon::now();
    }

    public function getTimeAttribute() {
        return DB::table('schedule_time')
            ->where('grade', $this->grade->number)->first();
    }



    public function getStudentsAttribute()
    {
        if ($this->all_class == 1) {
            return User::query()
                ->where('class', $this->grade_id)
                ->where('role_id', 2)
                ->orderBy('name')
                ->get();
        } elseif (!empty($this->grade_letter)) {
            return User::query()
                ->where('class', $this->grade_id)
                ->where('class_letter', $this->grade_letter)
                ->where('role_id', 2)
                ->orderBy('name')
                ->get();
        } elseif (!empty($this->group_id)) {
            return User::query()
                ->join('student_group_students', 'student_group_students.student_id', '=', 'users.id')
                ->where('student_group_students.group_id', $this->group_id)
                ->where('role_id', 2)
                ->orderBy('users.name')
                ->get();
        }
    }

    public static function getScheduleMain($teacher_id, $gradeId, $curatorClass)
    {
    	return self::query()
			->select(
				'schedule.*',
				'st.teacher_id',
				DB::raw("schedule_time.time_begin as lesson_time_begin"),
				DB::raw("schedule_time.time_end as lesson_time_end")
			)
			->leftJoin('grade', 'schedule.grade_id', '=', 'grade.id')
			->leftJoin('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
			->leftJoin('schedule_time', function ($join) {
				/** @var \Illuminate\Database\Query\JoinClause $join */
				$join
					->on('schedule_time.grade', '=', DB::raw('schedule.year - grade.year + 1'))
                    ->on('schedule_time.letter', '=','grade.letter')
					->on('schedule_time.lesson_number', '=', 'schedule.number');
			})
			->when($teacher_id, function ($query) use ($teacher_id) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('st.teacher_id', $teacher_id);
			})
			->when(!empty($curatorClass), function ($query) use ($curatorClass) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->orWhere('grade_id', $curatorClass);
			})
			->when($gradeId, function ($query) use ($gradeId) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('grade_id', $gradeId);
			})
            ->where(DB::raw('date(schedule.tms_end)'), '>', Carbon::now()->toDateString())
            ->where('schedule.year', Year::getInstance()->getYear())
			->orderBy('weekday', 'asc')
			->orderBy('number', 'asc')
			->get();
	}

	public static function getSchedulesByTeacherGradeLesson ($filter)
    {
		return self::query()
            ->leftJoin('schedule_teachers', 'schedule_teachers.schedule_id', '=', 'schedule.id')
			->where('schedule.grade_id', $filter['grade_id']['value'])
			->where('schedule.lesson_id', $filter['lesson_id']['value'])
			->where('schedule_teachers.teacher_id', $filter['teacher_id']['value'])
            ->where('year', Year::getInstance()->getYear())
/*			->when(!empty($filter['group_id']['value']), function ($query) use ($filter) {
				$query->where('group_id', $filter['group_id']['value']);
			})
			->when(!empty($filter['grade_letter']['value']), function ($query) use ($filter) {
				$query->where('grade_letter', $filter['grade_letter']['value']);
			})*/
			->when(!empty($filter['letter_group_id']['value']), function ($query) use ($filter) {
				$intFromFilter = (int)$filter['letter_group_id']['value'];
				if (0 === $intFromFilter) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('grade_letter', $filter['letter_group_id']['value']);
				} elseif (0 < $intFromFilter) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('group_id', $filter['letter_group_id']['value']);
				}
			})
			->when(!empty($filter['all_class']['value']), function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('all_class', $filter['all_class']['value']);
			})
           // ->where('schedule.tms_end', '>=', Carbon::parse($filter['date']['value'][0])->toDateString())
           // ->where('schedule.tms', '<=', Carbon::parse($filter['date']['value'][0])->toDateString())
			->get();
	}

	public static function getDateRangeFromInput($dateInput)
    {
		if($dateInput === null) {
			$dateInput = [
                Carbon::parse('02.09.'.(Year::getInstance()->getYear())),
                Carbon::now()->addWeeks(3)
			];
		}
		else {
			$dateInput = [
				Carbon::parse($dateInput[0]),
				Carbon::parse($dateInput[1])
			];
		}

		return $dateInput;
	}

	public static function getAllYearDateRange(): array {
		return [
			Carbon::parse('02.09.'.Year::getInstance()->getYear()),
			Carbon::parse('01.06.'.(Year::getInstance()->getYear()+1))
		];
	}

	/**
	 * @param int $grade_id
	 * @return array|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getLessonsByGradeId($grade_id) {
		return Schedule::where('grade_id', $grade_id)
			->join('lesson', 'lesson.id', '=', 'schedule.lesson_id')
			->groupBy('lesson_id')
			->orderBy('lesson.name', 'ASC')
			->get();
	}

	/**
	 * @param int $grade_id
	 * @param int $lesson_id
	 * @return array|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getTeachersByGradeIdAndLessonId($grade_id, $lesson_id) {
		return self::where('grade_id', $grade_id)
			->where('lesson_id', $lesson_id)
            ->join('schedule_teachers', 'schedule.id', '=', 'schedule_teachers.schedule_id')
			->join('users', 'users.id', '=', 'schedule_teachers.teacher_id')
			->groupBy('schedule_teachers.teacher_id')
			->orderBy('users.name', 'ASC')
			->get();
	}

	/**
	 * @param int $grade_id
	 * @param int $lesson_id
	 * @param int $teacher_id
	 * @return array|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getLettersGroupsByGradeIdAndLessonIdAndTeacherId($grade_id, $lesson_id, $teacher_id) {
		return self::where('grade_id', $grade_id)
			->where('lesson_id', $lesson_id)
			->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
			->where('st.teacher_id', $teacher_id)
			->groupBy(['grade_letter', 'group_id'])
			->get();
	}

	public static function createOptionsArray($lettersGroups, $forFilter = false) {
		$res = [];
		foreach ($lettersGroups as $lg) {

			if (!empty($lg->grade_letter)) {
				$label = $id = $lg->grade_letter;
			} elseif (!empty($lg->group_id) && !empty($lg->group)) {
				$id = $lg->group_id;
				$label = $lg->group->name;
			} else {
                $label = 'Весь класс';
                $id = 0;
            }

			if (isset($id, $label)) {
				if ($forFilter) {
					$res[$id] = $label;
				} else {
					$res['letter_group_id'][$id] = $label;
				}
			}
		}
		return $res;
	}

	/**
	 * @param Carbon $date_current
	 * @param $allYearDate
	 * @param $schedules
	 * @return array
	 */
	public static function getAllDatesForLesson($date_current, $allYearDate, $schedules) {
		$dates = [];
		$lesson_number = 1;

        $gradeNumber = $schedules[0]->grade->number;

        $intervals = Holiday::query()
            ->where('year', Year::getInstance()->getYear())
            ->where('period_type', $gradeNumber >= 10 ? 3 : 2 )
            ->orderBy('begin', 'asc')
            ->get();

        $intervalsDates = [];
        foreach ($intervals as $interval) {
            $intervalsDates[] = [Carbon::parse($interval->begin), Carbon::parse($interval->end)];
        }

		while($date_current <= $allYearDate) {
			foreach ($schedules as $schedule) {
				if ($date_current->dayOfWeek == $schedule->weekday && Carbon::parse($schedule->tms) <= $date_current && Carbon::parse($schedule->tms_end) >= $date_current ) {
                    $d = $date_current->clone();
				    foreach ($intervalsDates as $interval) {
                    	if (($interval[0] <= $d && $d <= $interval[1])) {
                    	    $dates[] = [
                    	        'date' => $d,
                    	        'dateYmd' => $d->format('Y-m-d'),
                                'Ymd' => $d->format('Ymd'),
                    	        'schedule' => $schedule,
                    	        'scheduleId' => $schedule->id,
                    	        'lessonId' => $schedule->lesson->id,
                    	        'number' => $lesson_number,
                    	        'gradeNumber' => $schedule->grade->number
                    	    ];
                    	    $lesson_number++;
                    	}
                    }
				}
			}
			$date_current->add('1 day');
		}
		return $dates;
	}

	public static function filterDays($dates, $dateInput)
    {
		$outOfFilterDates = [];
		$filteredDates = [];

		foreach ($dates as $d) {
			if($d['date'] >= $dateInput[0] && $d['date'] <= $dateInput[1]) {
				$filteredDates[] = $d;
			} else {
				$outOfFilterDates[] = $d;
			}
		}

		return ['dates' => $filteredDates, 'outOfFilterDates' => $outOfFilterDates];
	}

	/**
	 * Данные можно загрузить либо по schedule_id, либо по фильтру. Логика была построена на schedule_id
	 * Для того чтобы логику не трогать: если поиск данных идет по фильтру создаем переменную $proto_schedule
	 * будьто мы искали по schedule_id
	 * @param null|int $schedule_id
	 * @param null|self $proto_schedule
	 * @param self[]|null$schedules
	 */
	public static function createFakeProtoIfNotExist($schedule_id, &$proto_schedule, $schedules) {
    	if (empty($schedule_id) && !empty($schedules[0])) {
			$proto_schedule = clone $schedules[0];
		}
	}

	/**
	 * Если поиск пошел по schedule_id, то value в фильтрах будет пустой. Вот и мы его искусственно заполняем
	 * @param $request
	 * @param array $filter
	 * @param $proto_schedule
	 */
	public static function loadValuesToFilter($request, &$filter, $proto_schedule) {
		if (!empty($proto_schedule)) {
			$filter['teacher_id']['value'] = $proto_schedule->scheduleTeacher->first()->teacher_id??null;
			$filter['teacher_id']['options'] = self::getTeachersByGradeIdAndLessonId($proto_schedule->grade->id, $proto_schedule->lesson->id);
			$filter['lesson_id']['value'] = $proto_schedule->lesson->id;
			$filter['lesson_id']['options'] = self::getLessonsByGradeId($proto_schedule->grade->id);
			$filter['grade_id']['value'] = $proto_schedule->grade->id;



            $filter['date']['value'] = Score::getRange($request, $proto_schedule->grade->id);

/*            $filter['grade_letter']['value'] = $proto_schedule->grade_letter;
            $filter['grade_letter']['options'] = Schedule::query()
                ->where('grade_id', $proto_schedule->grade->id)
                ->groupBy('grade_letter')
                ->get();


            $filter['group_id']['value'] = $proto_schedule->group_id;
            $filter['group_id']['options'] =StudentGroup::query()
                ->where('grade_id', $proto_schedule->grade->id)
                ->where('lesson_id',  $proto_schedule->lesson->id)
                ->get();*/

			$filter['letter_group_id']['value'] = !empty($proto_schedule->grade_letter) ? $proto_schedule->grade_letter : $proto_schedule->group_id;
			$filter['letter_group_id']['options'] = Schedule::createOptionsArray(
				Schedule::getLettersGroupsByGradeIdAndLessonIdAndTeacherId(
				    $proto_schedule->grade->id,
                    $proto_schedule->lesson->id,
                    $proto_schedule->scheduleTeacher->first()->teacher_id ?? null),
				true
			);

			//dd($proto_schedule);
		}
	}

	public static function getStudentSchedule(User $student) {
        return Schedule::query()
            ->select(
                'lesson.id',
                'lesson.name'
            )
            ->leftJoin('lesson', 'lesson.id', '=', 'schedule.lesson_id')
            ->leftJoin('student_group_students', 'student_group_students.group_id', '=', 'schedule.group_id' )
            ->where('schedule.grade_id', $student->class)
            ->where('schedule.year', Year::getInstance()->getYear())
            ->where(function($query) use ($student) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query
                    ->where('all_class', 1)
                    ->orWhere('grade_letter', $student->class_letter)
                    ->orWhere('student_group_students.student_id', $student->id);
            })
            ->groupBy('lesson.id')
            ->orderBy('lesson.name', 'asc');
    }

    public function lessonNumber($date) {

	    $teacher = $this->scheduleTeacher->first();

	    //dd($teacher);

        $schedules = self::query()
            ->leftJoin('schedule_teachers', 'schedule_teachers.schedule_id', '=', 'schedule.id')
            ->where('schedule.grade_id', $this->grade_id)
            ->where('schedule.lesson_id', $this->lesson_id)
            ->when($teacher, function ($query) use ($teacher) {
                $query->where('schedule_teachers.teacher_id', $teacher->teacher_id);
            })
            ->when($this->grade_letter, function ($query) {
                $query->where('grade_letter', $this->grade_letter);
            })
            ->when($this->group_id, function ($query) {
                $query->where('group_id', $this->group_id);
            })
            ->where('all_class', $this->all_class)
            ->where('year', Year::getInstance()->getYear())
            ->get();

        $allYearDate = Schedule::getAllYearDateRange();
        $date_current = $allYearDate[0]->clone();


        $this->date = $date->clone();


        if ($schedules->isNotEmpty()) {
            $dates = Schedule::getAllDatesForLesson($date_current, $this->date, $schedules);

            //TODO кажется $dateInput не надо тут, мы далее ее определяем
            $dateInput = Score::getRange(request(), $this->grade_id);

            $gradeNumber = Grade::find($this->grade_id)->number ?? 1;


            $currenteriod = Holiday::query()
                ->where('year', Year::getInstance()->getYear())
                ->where('period_type', $gradeNumber >= 10 ? 3 : 2 )
                ->where('end', '>=', Carbon::now()->toDateString())
                ->orderBy('begin', 'asc')
                ->first();

            $dateInput = [
                Carbon::parse($currenteriod->begin)->format('d.m.Y'),
                Carbon::parse($currenteriod->end)->format('d.m.Y')
            ];

            $filtered = Schedule::filterDays($dates, array_map(function ($e) { return Carbon::parse($e); }, $dateInput));

            extract($filtered);

            $holidays = Holiday::getHolidays(Year::getInstance()->getYear());

            $holidaysArray = [];
            foreach ($holidays as $h) {
                $holidaysArray[] = [Carbon::parse($h->begin)->timestamp, Carbon::parse($h->end)->timestamp];
            }

            $dates = Score::setHolidaysInDates($dates, $holidaysArray);

            //dd($dates);

            return $dates[count($dates) - 1]['number'];
        }
        else {
            return 0;
        }



    }

}
