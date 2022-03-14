<?php

namespace App\Http\Controllers;

use App\Custom\Teacher;
use App\Custom\Validation;
use App\Grade;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function index(Request $request)
    {
		$filter = Teacher::createFilter($request);

        return view('teacher.index', [
			'filter' => $filter,
			'students'=> Teacher::searchResult($filter),
            'title'=> 'Сотрудники',
            'grades'=> Grade::getActive(),
            'fileName'=>'Сотрудники'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->role->name == 'admin' || Auth::user()->admin) {

            $student = new User();
            $student->role_id = 3;
            $student->active = 1;

            return view('teacher/form', [
                'student' => $student,
                'action' => route('teachers.store'),
                'method' => 'post',
                'grades' => Grade::getActive(),
                'title'=> 'Новый сотрудник',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
		Validation::emailValidation($request);

        $student = new User();
        $student->role_id = 3;
        $student->name = $request->get('name');
        $student->email = $request->get('email');
        $student->phone = $request->get('phone');
        $student->position = $request->get('position');
        $student->active = 1;
        $student->curator = $request->get('curator');

        if(!empty($request->get('password'))) {
            $student->password = Hash::make($request->get('password'));
            $student->passwordClean = $request->get('password');
        }

        $student->save();

        return redirect(route('teachers.index'));
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
        if(Auth::user()->role->name == 'admin' || Auth::user()->admin) {

            $student = User::find($id);

            return view('teacher/form', [
                'student' => $student,
                'action' => route('teachers.update', [$id]),
                'method' => 'put',
                'grades' => Grade::getActive(),
                'title'=> $student->name
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
		Validation::emailValidation($request, $id);

        $student = User::find($id);
        $student->role_id = 3;
        $student->name = $request->get('name');
        $student->email = $request->get('email');
        $student->phone = $request->get('phone');
        $student->position = $request->get('position');
        $student->active = 1;
        $student->curator = $request->get('curator');

        if(!empty($request->get('password'))) {
            $student->password = Hash::make($request->get('password'));
            $student->passwordClean = $request->get('password');
        }

        $student->save();

        return redirect(route('teachers.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return redirect(route('teachers.index'));
    }
}
