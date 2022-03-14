<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Custom\Period;
use App\Custom\Report;
use App\Custom\Year;
use App\Grade;
use App\Homework;
use App\Plan;
use App\Schedule;
use App\ScheduleComment;
use App\ScheduleHomework;
use App\Score;
use App\ScorePeriod;
use App\ScoreType;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use \Illuminate\Contracts\View\Factory;
use \Illuminate\View\View;

class ScoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function access() {
        return Auth::user()->role->name === 'teacher' ||
            Auth::user()->curator ||
            Auth::user()->role->name === 'admin' ||
            Auth::user()->admin;
    }

	/**
	 * @param string $blade
	 * @param Request $request
	 * @return Factory|View
	 */
	public function index(Request $request, $blade = 'index')
    {
		//@todo  срочный рефакторинг - много кода
        if (!$this->access()) {
            return abort(403);
        }

        Cookie::queue('userId', Auth::user()->id,100*60*24);

		$filter = Score::createFilter($request);




		//print_r($filter);
		$schedule_id = $request->get('schedule_id');

		if ($schedule_id) {
			$proto_schedule = Schedule::find($request->get('schedule_id'));
			Schedule::loadValuesToFilter($request, $filter, $proto_schedule);
		}

		$grade = Grade::find($filter['grade_id']['value']);

		Period::lastDaysPeriod($grade->number);
        $schedules = Schedule::getSchedulesByTeacherGradeLesson($filter);

        //dd($schedules);

        /** @var Schedule|Schedule[] $schedules*/
		if ($schedules->count() > 0 && null !== $filter['teacher_id']['value']) {
			Schedule::createFakeProtoIfNotExist($schedule_id, $proto_schedule, $schedules);

			$dateInput = Score::getRange($request, $proto_schedule->grade->id);

			/** @var Carbon[] $allYearDate */
			$allYearDate = Schedule::getAllYearDateRange();

			$date_current = $allYearDate[0]->clone();

			$dates = Schedule::getAllDatesForLesson($date_current, $allYearDate[1], $schedules);

			$scheduleIds = array_column($dates, 'scheduleId');
			$dateYmd = array_column($dates, 'dateYmd');
			$dateNumbers = array_column($dates, 'number');
			$gradeNumbers = array_column($dates, 'gradeNumber');
			$lessonIds = array_unique(array_column($dates, 'lessonId'));

			$filtered = Schedule::filterDays($dates, array_map(function ($e) { return Carbon::parse($e); }, $dateInput));

			/**	@var array $outOfFilterDates */
			/**	@var array $dates */
			extract($filtered);

			$homeworks = Homework::query()
				->whereIn('lessonId', $scheduleIds)
				->whereIn('date', $dateYmd)
				->get()->groupBy(['date', 'lessonNum']);

            $proto_schedule->plans = Plan::query()
				->whereIn('lesson_id', $lessonIds)
				->whereIn('lesson_num', $dateNumbers)
                ->when($proto_schedule->group_id, function ($query) use ($proto_schedule) {
					/** @var Builder $query */
					$query->where('group_id', $proto_schedule->group_id);
                })
				->when($proto_schedule->grade_letter, function ($query) use ($proto_schedule) {
					/** @var Builder $query */
					$query->where('grade_letter', $proto_schedule->grade_letter);
				},  function ($query) use ($proto_schedule)  {

				    if ($proto_schedule->grade->letter) {
                        $query->where('grade_letter', $proto_schedule->grade->letter);
				    }
				    else {
                        $query->whereNull('grade_letter');
                    }
                })
				->whereIn('grade_num', $gradeNumbers)

				->get()
                ->keyBy('lesson_num');

			$proto_schedule->homeworks=$homeworks;

			$proto_schedule->stud = $proto_schedule->students;
			$studentsId = array_column($proto_schedule->students->map->only(['id'])->toArray(), 'id');

			$scores = Score::query()
				->whereIn('student_id', $studentsId)
                ->where('lesson_id',  $proto_schedule->lesson_id)
				//->whereIn('schedule_id',$scheduleIds)
				->whereIn('date', $dateYmd)
				->get();

			$attendance = Attendance::query()
				->whereIn('student_id', $studentsId)
                ->where('lesson_id',  $proto_schedule->lesson_id)
				//->whereIn('schedule_id', $scheduleIds)
				->whereIn('date', $dateYmd)
				->get();

			foreach ($attendance as $a) {
				$proto_schedule->attendance[$a->student_id][$a->date][$a->lesson_num] = $a;
			}

			foreach ($scores as $s) {
				/** @var Score $s */
				$proto_schedule->scores[$s->student_id][$s->date][$s->lesson_num][] = $s;
				$period = Period::definePeriod(Carbon::parse($s->date)->timestamp, $proto_schedule->grade->id);
				Score::fillScores($s, $s->student_id, $period);
			}
			foreach (Score::$scores as $k => $s) {
				Score::fillTotalScore($k, $proto_schedule->grade->id);
			}

            $proto_schedule->comments = ScheduleComment::commentsForJournal(
                $studentsId,
                $proto_schedule->lesson_id,
                $dateYmd
            );

            $proto_schedule->isHomeworks = ScheduleHomework::commentsForJournal(
                $studentsId,
                $proto_schedule->lesson_id,
                $dateYmd
            );

            $scoresPeriod = ScorePeriod::query()
				->whereIn('student_id', $studentsId)
				->whereIn('lesson_id', $lessonIds)
                ->where('date', '>=', Carbon::parse(Year::getInstance()->getYearBegin()))
				//->whereBetween('date',$allYearDate)
				->get();

			foreach ($scoresPeriod as $sp) {
				/** @var ScorePeriod $sp */
				$proto_schedule->scoresPeriod[$sp->student_id][$sp->period_number] = $sp;
			}
		}
		Period::periodNames($filter['grade_id']['value']);

		$title = '';
		if (isset($proto_schedule)) {
            $title .= $proto_schedule->lesson->name.', '.$proto_schedule->grade->number.$proto_schedule->grade_letter;
            if (!empty($proto_schedule->group_id) && isset($proto_schedule->group)) {
                $title .= ', '.$proto_schedule->group->name;
            }
        }

		$holidaysArray = Score::getHolidays($request);

		//print_r($filter);

		return view('score/'.$blade, [
            'title' => $title,
            'schedule' => $proto_schedule ?? [],
            'dates' => isset($dates)?Score::setHolidaysInDates($dates, $holidaysArray): [],
			'filter' => $filter,
			'layout' => $blade,
			'types' => ScoreType::all(),
			'scores' => Score::ALL_SCORES,
            'minutes' => Attendance::MINUTES
		]);
    }

    public function myScore( Request $request) {
		if (Auth::user()->role_id == User::TEACHER && Auth::user()->children()->exists()) {
			$role = 'parent';
		} else {
			$role = Auth::user()->role->name;
		}
		$students = Score::allStudentsByRole($role);

        $student = $request->get('student_id', $students->first()->id ?? 0);
        if (!empty($student)) {
            $obStudent = User::find($student);
        } else {
            $obStudent = Auth::user();
        }

		Period::cutPeriodNames(null, true, false);
		$filter = Score::createFilterMyScore($request, $students, $student, $obStudent);
		$period = (int) filter_var($request->get('period'), FILTER_SANITIZE_NUMBER_INT);
		$scoreByPeriod = Score::scorePeriodByPeriod($obStudent->id, $period);


        return view('student-schedule', [
            'title'=> "Оценки ученика: {$obStudent->name} ({$obStudent->grade->number}{$obStudent->class_letter} класс)",
            'schedule' => Score::studentSchedule($obStudent),
            'grades' => Grade::getActive(),
            'students' => $students,
			'filter' => $filter,
			'periodKeys' => Period::definePeriodKey(),
            'currentGrade' => $obStudent->class,
            'currentStudent' => $student,
            'scores' => Score::scoresMyScore($obStudent, $filter),
            'homeworks' => ScheduleHomework::rowsByStudentAndPeriod($obStudent->id, $filter),
			'attendance' => Report::attendanceStudent($filter),
			'weightedAverage' => Report::weightedAverageScore($filter),
            'scorePeriod' => $scoreByPeriod->count() > 0 ? $scoreByPeriod : [],
            'scoreTotal' => Score::scorePeriodByPeriod($obStudent->id, 5),
            'scoreExam' => Score::scorePeriodByPeriod($obStudent->id, 6),
            'scoreAtt' => Score::scorePeriodByPeriod($obStudent->id, 7),
            'period' => $period,
            'obStudent' => $obStudent
        ]);
    }

    public function myScoreTotal( Request $request) {
        if (Auth::user()->role_id == User::TEACHER && Auth::user()->children()->exists()) {
            $role = 'parent';
        } else {
            $role = Auth::user()->role->name;
        }
        $students = Score::allStudentsByRole($role);

        $student = $request->get('student_id', $students->first()->id);
        if(!empty($student)) {
            $obStudent = User::find($student);
        } else {
            $obStudent = Auth::user();
        }

        Period::cutPeriodNames(null, true, false);
        $filter = Score::createFilterMyScore($request, $students, $student, $obStudent);
        $period = (int) filter_var($request->get('period'), FILTER_SANITIZE_NUMBER_INT);
        $scoreByPeriod = Score::scorePeriodByPeriod($obStudent->id, $period);


        return view('student-schedule-total', [
            'title'=> "Оценки ученика: {$obStudent->name} ({$obStudent->grade->number}{$obStudent->class_letter} класс)",
            'schedule' => Score::studentSchedule($obStudent),
            'grades' => Grade::getActive(),
            'students' => $students,
            'filter' => $filter,
            'periodKeys' => Period::definePeriodKey(),
            'currentGrade' => $obStudent->class,
            'currentStudent' => $student,
            'scores' => Score::scoresMyScore($obStudent, $filter),
            'attendance' => Report::attendanceStudent($filter),
            'weightedAverage' => Report::weightedAverageScore($filter),
            'scorePeriod' => $scoreByPeriod->count() > 0 ? $scoreByPeriod : [],
            'scoreTotal' => Score::scorePeriodByPeriod($obStudent->id, 5),
            'scoreExam' => Score::scorePeriodByPeriod($obStudent->id, 6),
            'scoreAtt' => Score::scorePeriodByPeriod($obStudent->id, 7),
            'period' => $period,
            'obStudent' => $obStudent
        ]);
    }

    public function edit(Request $request) {
        $scoreId = $request->get('score_id');
        $score = Score::find($scoreId);

        if($score !== null) {
            $scheduleId = $score->schedule_id;
            $studentId =  $score->student_id;
            $date = Carbon::parse($score->date);
        }
        else {
            $scheduleId = $request->get('schedule_id');
            $studentId = $request->get('student_id');
            $date = Carbon::parse($request->get('date'));
        }



        $schedule = Schedule::find($scheduleId);
        $student = User::find($studentId);


        return view('score/modal', [
            'title' => $student->name.', '.$schedule->lesson->name.' '.$date->format('d.m.Y'),
            'schedule' => $schedule,
            'student' => $student,
            'date' => $date,
            'types' => ScoreType::all(),
            'score' => $score,
            'scores' => Score::ALL_SCORES
        ]);
    }

    public function editAttendance(Request $request)
    {
        $scoreId = $request->get('attendance_id');
        $attendance = Attendance::find($scoreId);

        if ($attendance !== null) {
            $scheduleId = $attendance->schedule_id;
            $studentId =  $attendance->student_id;
            $date = Carbon::parse($attendance->date);
        }
        else {
            $scheduleId = $request->get('schedule_id');
            $studentId = $request->get('student_id');
            $date = Carbon::parse($request->get('date'));
        }

        $schedule = Schedule::find($scheduleId);
        $student = User::find($studentId);

        return view('score/modal-attendance', [
            'title' => $student->name.', '.$schedule->lesson->name.' '.$date->format('d.m.Y'),
            'schedule' => $schedule,
            'student' => $student,
            'date' => $date,
            'attendance' => $attendance,
            'minutes' => Attendance::MINUTES
        ]);
    }

    public function save(Request $request) {
        $scheduleId = $request->get('schedule_id');
        $date = Carbon::parse($request->get('date'));
        $schedule = Schedule::find($scheduleId);
        $student = User::find($request->get('student_id'));

        $data = [
            'student_id' => $student->id,
            'lesson_id' => $schedule->lesson->id,
            'schedule_id' => $schedule->id,
            'date' => $date->toDateString(),
            'lesson_num' => $schedule->number,
            'value' => $request->get('score_value'),
            'type_id' => $request->get('score_type'),
            'comment' => $request->get('score_comment'),
            'tms' => Carbon::now()->toDateTimeLocalString()
        ];

        DB::table('log')->insert([
            'class' => __CLASS__,
            'method' => __METHOD__,
            'operation' => 'Оценка',
            'userId' => $request->user()->id,
            'query' => json_encode($data),
            'tms' => Carbon::now()
        ]);

        Score::query()->updateOrInsert(
            [
                'id' => (int)$request->get('score_id'),
            ],
            $data
        );

// todo перенести в отдельный метод

        $student->score = Score::query()
            ->where('student_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', $date->toDateString())
            ->get();


        return view('score/scores', [
            'student' => $student
        ]);
    }

    public function delete(Request $request) {

        $scoreId = $request->get('score_id');
        $score = Score::find($scoreId);

        $scheduleId = $score->schedule_id;
        $studentId =  $score->student_id;
        $date = Carbon::parse($score->date);

        $student = User::find($studentId);
        $schedule = Schedule::find($scheduleId);

        // todo перенести в отдельный метод

        DB::table('log')->insert([
            'class' => __CLASS__,
            'method' => __METHOD__,
            'operation' => 'Оценка удалена',
            'userId' => $request->user()->id,
            'query' => json_encode([
                'student_id' => $student->id,
                'lesson_id' => $schedule->lesson->id,
                'schedule_id' => $schedule->id,
                'date' => $date->toDateString(),
                'lesson_num' => $schedule->number,
                'value' => $score->value,
                'type_id' => $score->type_id,
                'comment' => $score->comment,
                'tms' => Carbon::now()->toDateTimeLocalString()
            ]),
            'tms' => Carbon::now()
        ]);

        $score->delete();

        $student->score = Score::query()
            ->where('student_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', $date->toDateString())
            ->get();


        return view('score/scores', [
            'student' => $student
        ]);
    }

	public function filterUpdate(Request $request)
    {
		$grade_id = $request->get('grade_id');
		$lesson_id = $request->get('lesson_id');
		$teacher_id = $request->get('teacher_id');
		$res = [];

		if (null !== $lesson_id && null !== $grade_id && null !== $teacher_id) {
			$lettersGroups = Schedule::getLettersGroupsByGradeIdAndLessonIdAndTeacherId($grade_id, $lesson_id, $teacher_id);
			$res = Schedule::createOptionsArray($lettersGroups);
		} elseif (null !== $lesson_id && null !== $grade_id) {
			$teachers = Schedule::getTeachersByGradeIdAndLessonId($grade_id, $lesson_id);
			foreach ($teachers as $t) {
				$res['teacher_id'][$t->id] = $t->name;
			}
		} elseif (null !== $grade_id) {
			$lessons = Schedule::getLessonsByGradeId($grade_id);
			foreach ($lessons as $l) {
				$res['lesson_id'][$l->id] = $l->name;
			}
		}

		return $res;
	}

    public function saveAttendance(Request $request) {

        $scheduleId = $request->get('schedule_id');
        $date = Carbon::parse($request->get('date'));
        $schedule = Schedule::find($scheduleId);
        $student = User::find($request->get('student_id'));

        Attendance::query()->updateOrInsert(
            [
                'student_id' => $student->id,
                'lesson_id' => $schedule->lesson->id,
                'schedule_id' => $schedule->id,
                'date' => $date->toDateString(),
                'lesson_num' => $schedule->number
            ],
            [
                'student_id' => $student->id,
                'lesson_id' => $schedule->lesson->id,
                'schedule_id' => $schedule->id,
                'date' => $date->toDateString(),
                'lesson_num' => $schedule->number,
                'type' => $request->get('type'),
                'value' => $request->get('value'),
                'comment' => $request->get('comment'),
                'tms' => Carbon::now()->toDateTimeLocalString()
            ]
        );

// todo перенести в отдельный метод

        $student->attendance = Attendance::query()
            ->where('student_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', $date->toDateString())
            ->get();


        return view('score/attendance', [
            'student' => $student
        ]);
    }

    public function deleteAttendance(Request $request)
    {
        $scoreId = $request->get('attendance_id');
        $attendance = Attendance::find($scoreId);

        $scheduleId = $attendance->schedule_id;
        $studentId =  $attendance->student_id;
        $date = Carbon::parse($attendance->date);

        $student = User::find($studentId);
        $schedule = Schedule::find($scheduleId);

        // todo перенести в отдельный метод

        $attendance->delete();

        $student->attendance = Attendance::query()
            ->where('student_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', $date->toDateString())
            ->get();

        return view('score/attendance', [
            'student' => $student
        ]);
    }

	public function scorePeriodSave(Request $request) {
		$studentId = (int)$request->get('student_id');
		$user = User::getUserById($studentId);

		if (null === $user) {
			return false;
		}

		$scoreType = ScorePeriod::getPeriodTypeByClassNumber($user->grade->number);
		$lessonId = (int)$request->get('lesson_id');
        $gradeId = (int)$request->get('grade_id');
        $periodNumber =  (int)$request->get('period_number');

		ScorePeriod::query()->updateOrInsert(
			[
                'student_id' => $studentId,
                'lesson_id' => $lessonId,
                'grade_id' => $gradeId,
                'period_type' => $scoreType,
                'period_number' => $periodNumber
            ],
			[
				'student_id' => $studentId,
				'lesson_id' => $lessonId,
				'date' => Carbon::now()->format('Y-m-d'),
				'grade_id' => $gradeId,
				'group_id' => null,
				'grade_letter' => null,
				'value' => $request->get('value'),
				'type' => $request->get('type'),
				'teacher_id' => $request->get('teacher_id'),
				'period_type' => $scoreType,
				'period_number' => $periodNumber,
				'comment' => $request->get('comment'),
			]
		);

		return view('score/scores-period', [
			'score' => ScorePeriod::find(DB::getPdo()->lastInsertId())
		]);
	}

	public function deleteScorePeriod(Request $request)
    {
		$scoreId = $request->get('id');
		$score = ScorePeriod::find($scoreId);
		$score->delete();
		return ['response' => true];
	}

	public function scorePeriodEdit(Request $request)
    {
		$scorePeriodId = (int)$request->get('id');
		$scorePeriod = ScorePeriod::find($scorePeriodId);

		if (null === $scorePeriod) {
			$scorePeriod = new ScorePeriod();
			$scorePeriod->lesson_id = $request->get('lesson_id');
			$scorePeriod->grade_id = $request->get('grade_id');
			$scorePeriod->student_id = $request->get('student_id');
			$scorePeriod->teacher_id = $request->get('teacher_id');
			$scorePeriod->type = $request->get('type');
			$scorePeriod->period_number = $request->get('period_number');
		}

		return view('score/modal-score-period', [
			'title' => $scorePeriod->student->name.', Оценка периода',
			'scorePeriod' => $scorePeriod,
			'scores' => Score::ALL_SCORES
		]);
    }

}

