<?php

namespace App\Http\Controllers;

use App\Homework;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeworkController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
		setlocale(LC_TIME, 'ru_RU.UTF-8');//@todo мб надо это где-то в index файл определить?
		if (Auth::user()->role_id == User::TEACHER && Auth::user()->children()->exists()) {
			$role = 'parent';
		} else {
			$role = Auth::user()->role->name;
		}

        $date = $request->get('date', date('N')>=6 ? Carbon::parse('next monday')->format('d.m.Y') : Carbon::now()->format('d.m.Y'));
        $date = Carbon::parse($date);
		$var = Homework::defineVariables($role, $request);

		if (null !== $var['student']) {
			$day = Carbon::parse(date('Y-m-d', strtotime('monday this week', $date->timestamp)));//first day of the week
			$firstDay = clone $day;
			$lastDay = Carbon::parse(date('Y-m-d', strtotime('friday this week', $date->timestamp)));
			foreach ([1, 2, 3, 4, 5] as $d) {//@todo убрать этот некрасивый массив и оптимизировать запросы
				$schedule = Homework::scheduleForHomework($d, $var, $day);
				$dateCurrent = $day->toDateString();
				$scheduleWeek[] = [
					'scroll' => $dateCurrent === $date->toDateString(),
					'date' => $day->formatLocalized('%A %d %b %Y'),
					'homeworkAndScores' => Homework::homeworkAndScores($schedule, $dateCurrent, $var)
				];
				$day = $day->add(1, 'day');
			}
		}

		if(Auth::user()->id == 1) {
		    //dd($scheduleWeek);
        }

        return view('homework', [
            'title'=>$var['title'],
            'show_nav' => $var['show_nav'],
            'scheduleWeek'=>$scheduleWeek ?? [],
            'firstDay' => $firstDay ?? '',
            'lastDay' => $lastDay ?? '',
			'date' => $date ?? '',
			'currentStudent'=>$var['student'],
            'mode'=>$var['mode'],
            'students' => $var['students'],
            'printDay'=>(int)$request->get('printDay')
        ]);
    }

    public function getFormData(Request $request) {
        $homework = Homework::find($request->get('id'));

        return [
            'students' =>
                User::query()
                    ->where('role_id', 2)
                    ->where('class', $homework !== null ? $homework->grade : $request->get('grade'))
                    ->orderBy('name', 'asc')
                    ->get(),
            'grade_id' => $homework !== null ? $homework->grade : $request->get('grade'),
            'date' => $homework !== null ? $homework->date : $request->get('date'),
            'lesson_num' => $homework !== null ? $homework->lessonNum : $request->get('lesson_num'),
            'lesson_id' => $homework !== null ? $homework->lessonId : $request->get('lesson_id'),
            //'student_id' => $homework !== null ? $homework->student_id : $request->get('student_id'),
            'homework' => $homework ?? ['students' => ['ids' => []]],
            'child' => $homework !== null ? $homework->child : 0,
        ];
    }

    public function setHomework(Request $request) {

        $id = (int)$request->get('id');
        $students = (array)$request->get('student');

        $fields = [
            'grade' => $request->get('grade'),
            'date' =>  $request->get('date'),
            'lessonNum' => $request->get('lessonNum'),
            'lessonId' => $request->get('lessonId'),
            'child' => $request->get('child'),
            'text' => $request->get('text'),
            'tms' => Carbon::now()->toDateTimeLocalString()
        ];

        if(empty($id)) {
            $id = Homework::query()->insertGetId($fields);
        }
        else {
            Homework::query()->where('id', $id)->update($fields);
            DB::table('homework_child')->where('homework_id', $id)->delete();
        }

        if($fields['child'] && !empty($students)) {
            foreach ($students as $child_id) {
                DB::table('homework_child')->insert([
                    'homework_id' => $id,
                    'child_id' => $child_id,
                ]);
            }
        }

        DB::table('homework_log')->insert([
            'tms' => Carbon::now()->toDateTimeLocalString(),
            'author' => Auth::user()->id,
            'content' => $fields['text'],
            'date' =>  $fields['date'],
            'lessonNum' => $fields['lessonNum'],
            'lessonId' => $fields['lessonId'],
            'grade' => $fields['grade']
        ]);

        // todo log

        return Homework::find($id);

    }

    public function deleteHomework(Request $request) {
        $id = (int)$request->get('id');
        Homework::find($id)->delete();
    }


    public function getHomeworkForLesson(Request $request) {

        $homework = Homework::query()
            ->where('date', $request->get('date'))
            ->where('grade', $request->get('grade'))
            ->where('lessonNum', $request->get('lesson_num'))
            ->where('lessonId', $request->get('lesson_id'))
            ->get();

        return view('includes.calendar-homework', [
            'homework' => $homework,
            'can_edit' => true
        ]);
    }
}
