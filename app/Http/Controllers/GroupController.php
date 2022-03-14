<?php

namespace App\Http\Controllers;

use App\Custom\Year;
use App\Grade;
use App\Lesson;
use App\StudentGroup;
use App\StudentGroupStudent;
use App\User;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return bool
     */

    private function access() {
        return Auth::user()->role->name == 'teacher' || Auth::user()->curator || Auth::user()->role->name == 'admin' || Auth::user()->admin;
    }

    public function index(Request $request)
    {
        if(!$this->access()) {
            return abort(403);
        }

        if(Auth::user()->role->name == 'teacher' && !Auth::user()->admin ) {
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
				->groupBy('grade.id')
                ->get();

        }
        else {
            $lessons = Lesson::query()
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::getActive();
        }

        $filter = [
            'lesson_id' => [
                'tilte' => 'Предмет',
                'type' => 'select',
                'options' => $lessons,
                'value' => $request->get('lesson_id'),
                'name_field' => 'name'
            ],
            'grade_id' => [
                'tilte' => 'Параллель',
                'type' => 'select',
                'options' => $grades,
                'value' => $request->get('grade_id'),
                'name_field' => 'number'
            ]
        ];

        $groups = StudentGroup::query()
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->whereIn('grade_id', $grades->pluck('id'))
            ->where('year', Year::getInstance()->getYear())
            ->when($filter['lesson_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('lesson_id', $filter['lesson_id']['value']);
            })
            ->when($filter['grade_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('grade_id', $filter['grade_id']['value']);
            })
            ->get();

        return view('groups.index', [
            'title'=>'Группы',
            'groups' => $groups,
            'filter' => $filter
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {

        if(Auth::user()->role->name == 'teacher' && !Auth::user()->admin  ) {
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
				->groupBy('grade.id')
                ->get();

        }
        else {
            $lessons = Lesson::query()
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::getActive();
        }


        return view('groups.form', [
            'title'=>'Новая группа',
            'lessons' => $lessons,
            'grades' => $grades,
            'action' => '/groups',
            'method' => 'post'

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $group  = new StudentGroup();

        $group->name = $request->get('name');
        $group->lesson_id = $request->get('lesson_id');
        $group->grade_id = $request->get('grade_id');
        $group->year = Year::getInstance()->getYear();

        $group->save();

        return redirect("/groups/{$group->id}/edit");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $group = StudentGroup::find($id);

        if(Auth::user()->role->name == 'teacher' && !Auth::user()->admin  ) {
            $lessons = Lesson::query()
                ->select('lesson.*')
                ->join('schedule', 'schedule.lesson_id', '=', 'lesson.id')
				->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
				->where('st.teacher_id', Auth::user()->id)                ->groupBy('lesson.id')
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::query()
                ->select('grade.*')
                ->join('schedule', 'schedule.grade_id', '=', 'grade.id')
				->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
				->where('st.teacher_id', Auth::user()->id)
				->groupBy('grade.id')
                ->get();

        }
        else {
            $lessons = Lesson::query()
                ->orderBy('lesson.name')
                ->get();

            $grades = Grade::getActive();
        }

        return view('groups.form', [
            'title'=>'Редактировать группу',
            'lessons' => $lessons,
            'grades' => $grades,
            'group' => $group,
            'action' => '/groups/'.$id,
            'method' => 'put'

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse|Redirector
     */
    public function update(Request $request, $id)
    {
        $group = StudentGroup::find($id);

        $group->name = $request->get('name');
        $group->lesson_id = $request->get('lesson_id');
        $group->grade_id = $request->get('grade_id');

        $group->save();

        return redirect("/groups/{$group->id}/edit");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        StudentGroup::destroy($id);
        return redirect('/groups');
    }

    /**
     * @param Request $request
     * @param $grade_id
     * @param $lesson_id
     * @return Factory|View
     * @throws Exception
     */
    public function students(Request $request, $grade_id, $lesson_id) {
        //@todo rewrite method, to much queries
        if($request->isMethod('post')) {
            if (!empty($request->get('grade_id'))) {
                $grade_id = $request->get('grade_id');
            }

            if (!empty($request->get('lesson_id'))) {
                $lesson_id = $request->get('lesson_id');
            }
        }
        $lessons = Lesson::query()
            ->orderBy('lesson.name')
            ->get();

        $grades = Grade::query()
            ->get();

        $filter = [
            'lesson_id' => [
                'title' => 'Предмет',
                'type' => 'select',
                'options' => $lessons,
                'value' => $lesson_id,
                'name_field' => 'name'
            ],
            'grade_id' => [
                'title' => 'Параллель',
                'type' => 'select',
                'options' => $grades,
                'value' => $grade_id,
                'name_field' => 'number'
            ]
        ];

        $groups = StudentGroup::query()
            ->where('lesson_id', $lesson_id)
            ->where('grade_id', $grade_id)
            ->where('year', Year::getInstance()->getYear())
            ->get();

        if($request->isMethod('post')) {
            $data = [];
            $userIds = [];
            $groupIds = StudentGroup::query()
                ->where('lesson_id', $lesson_id)
                ->where('grade_id', $grade_id)
                ->pluck('id')->toArray();

            $students = array_filter($request->get('student'));
            foreach ($students as $k => $v) {
                $data[] = ['group_id' => (int)$v, 'student_id' => (int)$k];
                $userIds[] = (int)$k;//TODO зачем $userIds, мб надо убрать?
            }

            StudentGroupStudent::query()
                ->whereIn('group_id', $groupIds)
                ->delete();

            StudentGroupStudent::insert($data);
        }

        return view('groups.students', [
            'title' => 'Состав групп',
            'groups' => $groups,
            'filter' => $filter,
            'students' => User::query()
                ->where('class', $grade_id)
                ->where('role_id', 2)
                ->orderBy('name', 'asc')
                ->get()
        ]);
    }
}
