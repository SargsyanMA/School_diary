<?php

namespace App\Http\Controllers;

use App\Custom\Year;
use App\Plan;
use App\Lesson;
use App\Grade;
use App\Schedule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use \Illuminate\Contracts\Foundation\Application;
use \Illuminate\Contracts\View\Factory;
use \Illuminate\Http\RedirectResponse;
use \Illuminate\View\View;
use \PhpOffice\PhpSpreadsheet\Reader\Exception;

class PlanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index()
    {
        $plans = Plan::query()
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();

        $plans->load('lesson');

        return view('plan.index', [
            'title'=>'Календарный план',
            'plans' => $plans,
            'showExtraFunctional' => Auth::user()->role->name === 'teacher' || Auth::user()->role->name === 'admin'
        ]);
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return array
	 */
    public function create(Request $request)
    {
        if(Auth::user()->role->name === 'teacher' && !Auth::user()->admin ) {
            $lessons = Lesson::query()
                ->select('lesson.*')
                ->join('schedule', 'schedule.lesson_id', '=', 'lesson.id')
				->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
				->where('st.teacher_id', Auth::user()->id)
                ->groupBy('lesson.id')
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::query()
                ->select('grade.*')
                ->join('schedule', 'schedule.grade_id', '=', 'grade.id')
				->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
				->where('st.teacher_id', Auth::user()->id)
                ->groupBy('grade.year')
                ->orderBy('grade.year', 'desc')
                ->get();
        }
        else {
            $lessons = Lesson::query()
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::query()
                ->groupBy('grade.year')
                ->orderBy('grade.year', 'desc')
                ->get();
        }

        $plan = new Plan();
        $plan->lesson_num = Input::get('lesson_num');
        $plan->lesson_id = Input::get('lesson_id');
        $plan->grade_num = Input::get('grade_num');
        $plan->grade_letter = Input::get('grade_letter');
        $plan->group_id = Input::get('group_id');

        //@todo to model
        $groups =  Schedule::query()
            ->select('schedule.group_id', 'schedule.grade_letter', 'student_groups.name' )
            ->leftJoin('student_groups', 'student_groups.id', '=', 'schedule.group_id')
            ->whereIn(
                'schedule.grade_id',
                Grade::where('year', Year::getInstance()->getYear() - $plan->grade_num + 1)->pluck('id') ?? 1
            )
            ->where('schedule.lesson_id', $plan->lesson_id)
            ->where('schedule.year', Year::getInstance()->getYear())
            ->groupBy(['grade_letter', 'group_id'])
            ->get();

        //dd($groups);

		$groups = Schedule::createOptionsArray(
			$groups,
			true
		);

		if($request->ajax()){
			foreach ($grades as $grade) {
				$grade->number = $grade->getNumberAttribute();
			}

			return [
				'lessons' => $lessons,
				'grades' => $grades,
				'plan' => $plan,
                'groups' => $groups
			];
		} else {
			return view('plan.form', [
				'title'=>'Новая тема урока',
				'lessons' => $lessons,
				'grades' => $grades,
				'action' => '/plan',
				'method' => 'post',
				'plan' => $plan,
                'groups' => $groups
			]);
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $plan  = new Plan();

        $plan->title = $request->get('title');
        $plan->lesson_id = $request->get('lesson_id');
        $plan->grade_num = $request->get('grade_num');
        $plan->lesson_num = $request->get('lesson_num');

		$groupLetter = $request->get('group_id');
		if (empty($groupLetter)) {
			$plan->group_id = null;
			$plan->grade_letter = null;
		} elseif (is_numeric($groupLetter)) {
			$plan->group_id = $groupLetter;
			$plan->grade_letter = null;
		} else {
			$plan->grade_letter = $groupLetter;
			$plan->group_id = null;
		}

        $plan->save();
		if($request->ajax()){
			return ['response' => true];
		} else {
			return redirect()->action('PlanController@index');
		}
    }

    /**
     * Display the specified resource.
     *
     * @return false
     */
    public function show()
    {
        return false;
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @param Request $request
	 * @return array
	 */
    public function edit($id, Request $request)
    {
        $plan = Plan::find($id);

        if(Auth::user()->role->name === 'teacher' && !Auth::user()->admin) {
            $lessons = Lesson::query()
                ->select('lesson.*')
                ->join('schedule', 'schedule.lesson_id', '=', 'lesson.id')
				->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
				->where('st.teacher_id', Auth::user()->id)
				->groupBy('lesson.id')
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::query()
                ->select('grade.*')
                ->join('schedule', 'schedule.grade_id', '=', 'grade.id')
				->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
				->where('st.teacher_id', Auth::user()->id)
                ->groupBy('grade.year')
                ->orderBy('grade.year', 'desc')
                ->get();

        }
        else {
            $lessons = Lesson::query()
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::query()
                ->groupBy('grade.year')
                ->orderBy('grade.year', 'desc')
                ->get();
        }

		//@todo to model
        $groups =  Schedule::query()
            ->select('schedule.group_id', 'schedule.grade_letter', 'student_groups.name' )
            ->leftJoin('student_groups', 'student_groups.id', '=', 'schedule.group_id')
            ->whereIn(
                'schedule.grade_id',
                Grade::where('year', Year::getInstance()->getYear() - $plan->grade_num + 1)->pluck('id') ?? 1
            )
            ->where('schedule.lesson_id', $plan->lesson_id)
            ->where('schedule.year', Year::getInstance()->getYear())
            ->groupBy('grade_letter')
            ->groupBy('group_id')
            ->get();



		$groups = Schedule::createOptionsArray(
			$groups,
			true
		);

		$selectedGroupLetter = null !== $plan->grade_letter? $plan->grade_letter:(null !== $plan->group_id? $plan->group_id:null);

		if($request->ajax()){
			foreach ($grades as $grade) {
				$grade->number = $grade->getNumberAttribute();
			}

			return [
				'lessons' => $lessons,
				'grades' => $grades,
				'plan' => $plan,
				'selectedGroupLetter' => $selectedGroupLetter,
                'groups' => $groups
			];
		} else {
			return view('plan.form', [
				'title'=>'Новая тема урока',
				'lessons' => $lessons,
				'grades' => $grades,
				'action' => '/plan',
				'method' => 'post',
				'plan' => $plan,
				'selectedGroupLetter' => $selectedGroupLetter,
				'groups' => $groups

			]);
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);
        $plan->title = $request->get('title');
        $plan->lesson_id = $request->get('lesson_id');
        $plan->grade_num = $request->get('grade_num');
        $plan->lesson_num = $request->get('lesson_num');
        $plan->group_id = $request->get('group_id');
        $plan->save();

		return ['response' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Plan::destroy($id);
        return redirect('/plan');
    }

    /**
     * Attention: на странице plan была форма для загрузки CSV, мы ее убрали, но action оставили. Если оно не нужно то,
     * можно убрать
     * @deprecated
     * @param Request $request
     * @param int $step
     * @return Application|Factory|RedirectResponse|View
     * @throws Exception
     */
    public function upload(Request $request, $step = 1)
    {
        if ($step == 1) {
            $file = $request->file('plan');

            $new_file_name = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(storage_path() . '/ktp_import/', $new_file_name);

            $inputFileType = \PHPExcel_IOFactory::identify($path);
            $reader =\PHPExcel_IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);

            $rows = $spreadsheet->getWorksheetIterator()->current()->toArray();

            return view('plan.upload', [
                'rows' => $rows,
                'grades' => Grade::getActive(),
                'columns' => [
                    0 => 'lesson_id',
                    1 => 'group_id',
                    3 => 'lesson_num',
                    4 => 'title'
                ],
                'teachers' => User::query()
                    ->whereIn('role_id', [1, 3])
                    ->orderBy('name')
                    ->get()
            ]);
        } elseif($step == 2) {
            $plans = $request->get('plan');

            if(!empty($plans)) {
                foreach ($plans as $item) {
                    $plan = new Plan();

                    $plan->lesson_id = $item['lesson_id'];
                    $plan->lesson_num = $item['lesson_num'];
                    $plan->grade_num = $request->get('grade_num');
                    $plan->teacher_id = $request->get('teacher_id');
                    $plan->group_id = $item['group_id'];
                    $plan->title = $item['title'];
                    $plan->save();
                }
            }

            return redirect()->action('PlanController@index');
        }
    }

    /**
     * Либо показывает страницу для загрузки файлов, либо загружает файлов
     * @param Request $request
     * @return Application|Factory|View
     */
    public function uploadFile(Request $request)
    {
        if ('POST' === $request->method()) {
            Plan::loadPlanFromFile($request);
            return redirect('/plan');
        }

        $plan  = new Plan();
        $plan->grade_num = $request->get('grade_num');
        $plan->lesson_id = $request->get('lesson_id');

        $groups =  Schedule::query()
            ->select('schedule.group_id', 'schedule.grade_letter', 'student_groups.name' )
            ->leftJoin('student_groups', 'student_groups.id', '=', 'schedule.group_id')
            ->whereIn(
                'schedule.grade_id',
                Grade::where('year', Year::getInstance()->getYear() - $plan->grade_num + 1)->pluck('id') ?? 1
            )
            ->where('schedule.lesson_id', $plan->lesson_id)
            ->where('schedule.year', Year::getInstance()->getYear())
            ->whereNotNull('group_id')
            ->groupBy('group_id')

            ->get();


        return view('plan.upload-file', [
            'title'=>'Загрузка плана',
            'lessons' => Lesson::lessonsForRole(),
            'grades' => Grade::gradesForRole(),
            'groups' => $groups,
            'plan' => $plan
        ]);
    }

}
