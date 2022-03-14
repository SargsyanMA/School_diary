<?php

namespace App;

use App\Custom\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * App\Homework
 *
 * @property int $id
 * @property int $grade
 * @property string $date
 * @property int $lessonNum
 * @property int $lessonId
 * @property int $child
 * @property string|null $text
 * @property string $tms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $students
 * @property-read \App\Grade $oGrade
 * @property-read \App\Schedule $schedule
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereLessonNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereTms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Homework whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Homework extends Model
{

    protected $table = 'homework';

    public function getStudentsAttribute() {
        return $this->belongsToMany('App\User', 'homework_child', 'homework_id' , 'child_id')->orderBy('name')->get();
    }

    public function getStudentsIdsAttribute() {
        return $this->students->pluck('id')->toArray();
    }

    public static function getWeek($year,$weekNumber, $mode='class', $grade=0, $teacher=0, $student=0 ) {
        global $weekDict;

        $holiday = Holiday::all();

        /*foreach($holiday as $item) {
            $holidayTime[]=[date('Ymd',strtotime($item['begin'])),date('Ymd',strtotime($item['end']))];
        }*/

        $curYear=date('Y');
        $curMonth=date('n');
        $firstSeptWeek=date('W', strtotime($curYear.'-09-01'));
        $firstSeptTime=strtotime($curYear.'-09-01');
        if ($year==$curYear && $weekNumber<$firstSeptWeek && $curMonth>5) {
            $weekNumber=$firstSeptWeek;
        }

        $week=[];

        $monday=date('Y-m-d', strtotime($year."W".sprintf('%02d', $weekNumber).'1'));
        $schedule=Schedule::all();

        $homework = Homework::all();

        if (empty($schedule)) {
            return false;
        }

        for($day=1; $day<=5; $day++)
        {
            if (strtotime($year."W".$weekNumber.$day)< $firstSeptTime && $year==$curYear && $curMonth>5)
                continue;
            $week[$day]=array(
                'date'=>date('d.m.Y', strtotime($year."W".sprintf('%02d', $weekNumber).$day)),
                'name'=>$weekDict[$day],
                'dateInt'=>date('Ymd', strtotime($year."W".sprintf('%02d', $weekNumber).$day))
            );

            /*foreach($holidayTime as $time) {
                if ($time[0] <= $week[$day]['dateInt'] && $time[1] >= $week[$day]['dateInt']) {
                    $week[$day]['class']='holiday';
                }
            }*/

            $dayLessons=array();
            /*foreach ($schedule['lessons'] as $lessonNum => $lessons) {
                if(!empty($lessons[$day]['lessons'])) {
                    foreach ($lessons[$day]['lessons'] as &$lsn) {
                        $lsn['homework']=$homework[$day][$lessonNum][$lsn['id']];
                    }
                    $dayLessons[] = $lessons[$day];
                }
            }*/
            $week[$day]['lessons']=$dayLessons;
        }
        return $week;
    }

	public static function reportQuery($filter) {
		return self::query()
			->leftJoin('schedule', 'schedule.id', 'homework.lessonId')
			->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
			->when($filter['date']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->whereBetween('homework.date',
					array_map(
						function($e) {
							return Carbon::parse($e)->toDateTimeLocalString();
						},
						$filter['date']['value']
					)
				);
			})
			->when($filter['grade_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('homework.grade', $filter['grade_id']['value']);
			})
			->when($filter['lesson_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('schedule.lesson_id', $filter['lesson_id']['value']);
			})
			->when($filter['teacher_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('st.teacher_id', $filter['teacher_id']['value']);
			})
			->get();
	}

	public static function defineVariables($role, Request $request) {
		if($role == 'student') {
			$students = User::where('id', Auth::user()->id)->get();

			$var = [
				'mode' => 'student',
				'show_nav' => false,
				'title' => "Домашние задания ученика: ".Auth::user()->name,
				'students' => $students,
				'grades'=> Grade::whereIn('id', $students->pluck('class')),
				'student' => Auth::user()->id,
				'grade' => Auth::user()->class
			];
		} elseif($role == 'parent') {
            $students = User::query()
                ->select('users.*')
                ->leftJoin('grade', 'grade.id', '=', 'users.class')
                ->whereIn('users.id', StudentParent::getStudentsId())
                ->orderBy('grade.year', 'desc')
                ->orderBy('users.name', 'asc')
                ->get();

            $studentId =  $request->get('student', $students->first()->id ?? 0);
            $student = User::find($studentId);

            $grades = Grade::whereIn('id', $students->pluck('class'))->get();



			$var = [
				'mode' => 'student',
				'show_nav' => true,
				'title' => "Домашние задания ученика: {$student->name} ({$student->grade->number}{$student->class_letter} класс)",
				'students' => $students,
				'grades' => $grades,
				'student' => $request->get('student', $students->first()->id ?? 0),
				'grade' => $request->get('grade')?? $grades[0]->id
			];
		} else {
            $students = User::query()
                ->where('role_id', User::STUDENT)
                ->when(Auth::user()->role->name == 'student', function ($query) {
                    /** @var \Illuminate\Database\Eloquent\Builder $query */
                    $query->where('id', Auth::user()->id);
                })
                ->orderBy('name')
                ->get();

            $var = [
				'mode' => $request->get('mode', 'student'),
				'show_nav' => true,
				'title' => "Дневник",
				'students' => $students,
				'grades' => Grade::getActive(),
				'student' => $request->get('student', $students->first()->id ?? 0)
			];
		}

		return $var;
	}

	public static function scheduleForHomework($date, $var, $day) {
		$student = User::find($var['student']);

		return Schedule::query()
			->select(
				'schedule.*',
				DB::raw("schedule_time.time_begin as lesson_time_begin"),
				DB::raw("schedule_time.time_end as lesson_time_end")
			)
			->leftJoin('grade', 'schedule.grade_id', '=', 'grade.id')
			->leftJoin('student_group_students', 'student_group_students.group_id', '=', 'schedule.group_id' )
			->leftJoin('schedule_time', function($join) {
				/** @var \Illuminate\Database\Query\JoinClause $join */
				$join
					->on('schedule_time.grade', '=', DB::raw('schedule.year - grade.year + 1'))
					->on('schedule_time.lesson_number', '=', 'schedule.number');
			})
			->where('schedule.grade_id', $student->class)
			->where('schedule.year', Year::getInstance()->getYear())
			->where(function($query) use ($student) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query
                    ->where('schedule.all_class', 1)
                    ->orWhere('schedule.grade_letter', $student->class_letter)
                    ->orWhere('student_group_students.student_id', $student->id);
            })

			->where('weekday', $date)
            ->where('schedule.tms_end', '>=', $day->toDateString())
            ->where('schedule.tms', '<=', $day->toDateString())
			->groupBy('schedule.lesson_id', 'schedule.number')
			->orderBy('number', 'asc')
			->get();
	}

	public static function homeworkAndScores($schedule, $date, $var) {
		foreach ($schedule as &$item) {
			$item->homework = Homework::query()
                ->leftJoin('homework_child', 'homework_id', '=', 'homework.id')
                ->where(function ($query) use ($var) {
                    $query
                        ->where('child', 0)
                        ->orWhere('homework_child.child_id', $var['student']);
                })
				->where('date', $date)
				->where('lessonId', $item->id)
				->get();

			if(!empty($var['student'])) {
				$item->score = Score::query()
					->where('student_id', $var['student'])
					->where('schedule_id', $item->id)
					->where('date', $date)
					->get();
			}
		}
		return $schedule;
	}

    public function oGrade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade');
    }

    public function schedule()
    {
        return $this->hasOne('App\Schedule', 'id', 'lessonId');
    }



}
