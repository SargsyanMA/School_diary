<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Grade;
use App\User;
use Illuminate\Http\Request;
use TCG\Voyager\Models\Role;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 */

    public function __construct()
    {
        $this->middleware('auth');
    }

	public function export()
	{
		return Excel::download(new InvoicesExport, 'invoices.xlsx');
	}

    public function index(Request $request)
    {

        $filter = [
            'role' => $request->get('role'),
            'grade' => $request->get('grade'),
        ];

        return view('user.index', [
            'filter' => $filter,
            'roles' => Role::all(),
            'users'=>User::query()
                ->when($filter['role'], function ($query) use ($filter) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('role_id', $filter['role']);
                })
                ->when($filter['grade'], function ($query) use ($filter) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('class', $filter['grade']);
                })
                ->get(),
            'title'=> 'Пользователи',
            'grades'=> Grade::getActive(),
            'fileName'=>'пользователи'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
