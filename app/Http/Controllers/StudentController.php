<?php

namespace App\Http\Controllers;

use App\Custom\Report;
use App\Custom\Validation;
use App\Custom\Year;
use App\Grade;
use App\Score;
use App\StudentSocial;
use App\StudentAchievement;
use App\StudentAchievementType;
use App\StudentComment;
use App\StudentParent;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Custom\Student;

class StudentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 */

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function access() {
        return Auth::user()->role->name == 'teacher' || Auth::user()->curator || Auth::user()->role->name == 'admin' || Auth::user()->admin;
    }

    public function index(Request $request)
    {

        if(!$this->access()) {
            return abort(403);
        }

		$filter = Student::createFilter($request);

        return view('student.index', [
            'current_grade' => $filter['grade_id']['value'],
			'filter' => $filter,
            'students'=> Student::searchResult($filter),
            'title'=> 'Ученики',
            'fileName'=>'ученики'
        ]);
    }

    public function create()
    {
        if(!$this->access()) {
            return abort(403);
        }

        $student = new User();
        $student->role_id = 2;
        $student->active = 1;

        return view('student/form', [
            'student' => $student,
            'parentsIds' => [],
            'action' => route('students.store'),
            'method' => 'post',
            'grades' => Grade::getActive(),
            'parents' => User::query()
                ->whereIn('role_id', [1,3,4])
                ->orderBy('name')
                ->get()
        ]);

    }

    public function store(Request $request) {

        if(!$this->access()) {
            return abort(403);
        }

		Validation::emailValidation($request);

		$student = new User();
        $student->role_id = 2;
        $student->name = $request->get('name');
        $student->email = $request->get('email');
        $student->phone = $request->get('phone');
        $student->class = $request->get('grade_id');
        $student->class_letter = $request->get('group');
        $student->birthdate = Carbon::parse($request->get('birthDate'))->toDateString();
        $student->relation = $request->get('relation');
        $student->note = $request->get('note');
        $student->active = 1;

        if(!empty($request->get('password'))) {
            $student->password = Hash::make($request->get('password'));
            $student->passwordClean = $request->get('password');
        }

        $student->save();

		StudentParent::deleteByStudentId(array_filter($request->get('parent', [])), $student->id);

		return redirect(route('students.index'));
    }


    public function edit($id)
    {
        if(!$this->access()) {
            return abort(403);
        }

        $student = User::find($id);
        $parentsIds = [];
        foreach ($student->parents as $p) {
            $parentsIds[] = $p->parent_id;
        }

        return view('student/form', [
            'student' => $student,
            'parentsIds' => $parentsIds,
            'action' => route('students.update', [$id]),
            'method' => 'put',
            'grades' => Grade::getActive(),
            'parents' => User::query()
                ->whereIn('role_id', [1,3,4])
                ->orderBy('name')
                ->get()
        ]);

	}

    public function update(Request $request, $id) {

        if(!$this->access() && $id != $request->user()->id) {
            return abort(403);
        }

		Validation::emailValidation($request, $id);

		$student = User::find($id);
        $student->role_id = 2;
        if($request->get('name') !== null)
            $student->name = $request->get('name');
        if($request->get('email') !== null)
            $student->email = $request->get('email');
        if($request->get('phone') !== null)
            $student->phone = $request->get('phone');
        if($request->get('grade_id') !== null)
            $student->class = $request->get('grade_id');
        if($request->get('group') !== null)
            $student->class_letter = $request->get('group');
        if($request->get('birthDate') !== null)
            $student->birthdate = Carbon::parse($request->get('birthDate'))->toDateString();
        if($request->get('relation') !== null)
            $student->relation = $request->get('relation');
        if($request->get('note') !== null)
            $student->note = $request->get('note');
        if($request->file('photo') !== null) {
            $avatar= $request->file('photo')->store(
                'img/user', 'public'
            );
            $student->avatar = $avatar;
        }

        $student->active = 1;

        if(!empty($request->get('password'))) {
            $student->password = Hash::make($request->get('password'));
            $student->passwordClean = $request->get('password');
        }

        $student->save();

        if($request->get('parent') !== null)
		    StudentParent::deleteByStudentId(array_filter($request->get('parent', [])), $id);

        return back();
    }

	public function filterUpdate(Request $request) {

		$grade_id = $request->get('grade_id');
		$res = [];

		if (null !== $grade_id) {
			$students = Student::getStudentsByGradeId($grade_id);
			foreach ($students as $s) {
				$res[$s->id] = $s->name;
			}
		}
		sort($res);

		return $res;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @param null|string $layout
	 * @return \Illuminate\Http\Response
	 */
    public function show(Request $request, $id, $layout = null)
    {
        if(!$this->access() && $id != $request->user()->id) {
            return abort(403);
        }

        $student = User::find($id);

		$filter = [
			'student_id' => [
				'value' => $id
			],
			'date' => [
				'value' => [
					Carbon::parse('01.09.'.Year::getInstance()->getYear())->format('d.m.Y'),
					Carbon::now()->format('d.m.Y')
				]
			],
		];

		return view('student.detail', [
			'layout' =>$layout,
			'student' => $student,
			'schedule' => null !== $student? Score::studentSchedule($student):[],
			'achievements' => StudentAchievement::query()
                ->where('student_id', $id)
                ->get(),
			'social' => StudentSocial::query()
				->where('student_id', $id)
				->get(),
            'achievement_types' => StudentAchievementType::all(),
			'scores' => Report::searchResult($filter),
			'attendance' => Report::attendanceStudent($filter),
			'weightedAverage' => Report::weightedAverageScore($filter)
        ]);

    }

    public function addComment($id, Request $request) {
        $comment = new StudentComment();
        $comment->student_id = $id;
        $comment->author_id = Auth::user()->id;
        $comment->text = $request->get('text');
        $comment->save();

        return redirect('students/'.$id);
    }


    public function addAchievement($id, Request $request) {
        $comment = new StudentAchievement();
        $comment->student_id = $id;
        $comment->type_id = $request->get('type_id');;
        $comment->text = $request->get('text');

        if($request->file('file') !== null) {
            $file= $request->file('file')->store(
                'img/achievement', 'public'
            );
            $comment->file = $file;
        }
        $comment->save();

        return redirect('students/'.$id);
    }

	public function editAchievement(Request $request) {
		$id = $request->get('achievementId');
		$model = StudentAchievement::find($id);

		if ($request->get('achievementAction') === 'delete') {
			$model->destroy($id);
		} else {
			$model->update($request->all());
		}

        if($request->file('file') !== null) {
            $file= $request->file('file')->store(
                'img/achievement', 'public'
            );
            $model->file = $file;
        }
        $model->save();


		return redirect('students/'.$model->student_id);
	}

	public function addSocial($id, Request $request) {
		$comment = new StudentSocial();
		$comment->value = $request->get('value');
		$comment->student_id = $id;
		$comment->comment = $request->get('comment');
		$comment->save();

		return redirect('students/'.$id);
	}

	public function editSocial(Request $request) {
    	$id = $request->get('socialId');
		$model = StudentSocial::find($id);

		if ($request->get('socialAction') === 'delete') {
			$model->destroy($id);
		} else {
			$model->update($request->all());
		}

		return redirect('students/'.$model->student_id);
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!$this->access()) {
            return abort(403);
        }

        User::destroy($id);
        return redirect('students');
    }

}
