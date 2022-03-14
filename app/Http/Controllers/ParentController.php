<?php

namespace App\Http\Controllers;

use App\Custom\StudentParent;
use App\Custom\Validation;
use App\Grade;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @internal param Request $request
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

		$filter = StudentParent::createFilter($request);

		$parents = User::
			select('users.*', 'students.name as student_name', 'students.class')
            ->leftJoin('students_parents as sp', 'sp.parent_id', '=', 'users.id')
            ->leftJoin('users as students', 'sp.student_id', '=', 'students.id')
            ->leftJoin('grade', 'grade.id', '=', 'students.class')
			->where('users.parent', 1)
            ->when($filter['grade_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('students.class', $filter['grade_id']['value'] );
            })
            ->when($filter['name']['value'], function ($query) use ($filter) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('users.name', 'like', '%' . $filter['name']['value'] . '%');
            })
			->groupBy('users.id')
            ->orderBy('grade.year', 'desc')
			->orderBy('users.name', 'asc')
			->get();

        return view('parent.index', [
            'current_grade' => $filter['grade_id']['value'],
            'parents' => $parents,
            'title'=> 'Родители',
            'grades'=> Grade::getActive(),
            'fileName'=>'Родители',
            'filter' => $filter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!$this->access()) {
            return abort(403);
        }

        $student = new User();
        $student->role_id = 4;
        $student->active = 1;
        $student->parent = 1;

        return view('parent/form', [
            'student' => $student,
            'action' => route('parents.store'),
            'method' => 'post',
            'grades' => Grade::getActive(),
            'title'=> 'Новый родитель',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(!$this->access()) {
            return abort(403);
        }

		Validation::emailValidation($request);

		$student = new User();
        $student->role_id = 4;
        $student->name = $request->get('name');
        $student->email = $request->get('email');
        $student->phone = $request->get('phone');
        $student->note = $request->get('note');
        $student->relation = $request->get('relation');
        $student->active = 1;
        $student->parent = 1;

        if(!empty($request->get('password'))) {
            $student->password = Hash::make($request->get('password'));
            $student->passwordClean = $request->get('password');
        }

        $student->save();

        return redirect(route('parents.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!$this->access()) {
            return abort(403);
        }

        $student = User::find($id);

        return view('parent/form', [
            'student' => $student,
            'action' => route('parents.update', [$id]),
            'method' => 'put',
            'grades' => Grade::getActive(),
            'title'=> $student->name,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$this->access()) {
            return abort(403);
        }

		Validation::emailValidation($request, $id);

		$student = User::find($id);
        $student->role_id = 4;
        $student->name = $request->get('name');
        $student->email = $request->get('email');
        $student->phone = $request->get('phone');
        //$student->active = 1;
        $student->parent = 1;

        if(!empty($request->get('password'))) {
            $student->password = Hash::make($request->get('password'));
            $student->passwordClean = $request->get('password');
        }

        $student->save();

        return redirect(route('parents.index'));
    }

	public function filterUpdate(Request $request) {
		$grade_id = $request->get('grade_id');
		$res = [];

		if (null !== $grade_id) {
			$parents = StudentParent::getParentsByGradeId($grade_id);
			foreach ($parents as $p) {
				$res[$p->id] = $p->name;
			}
		}
		sort($res);

		return $res;
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
        return redirect(route('parents.index'));
    }
}
