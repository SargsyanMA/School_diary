<?php

namespace App\Http\Controllers;

use App\FormTest;
use App\FormTestResult;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class FormTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filter = Input::get('filter');
        $tests = FormTest::query()
            ->when($filter, function ($query) use ($filter) {
                $query->where('name', 'like', "%{$filter}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('forms/test/index', [
            'tests' => $tests,
            'filter' => $filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forms/test/form', [
            'action' => route('test.store'),
            'method' => 'post',
            'test' => new FormTest()
        ]);
    }

    public function editResult($id, $result_id)
    {
        return view('forms/test/result-form', [
            'action' => route('test.storeresult', [$id, $result_id]),
            'method' => 'post',
            'test' => FormTest::find($id),
            'test_result' => empty($result_id) ? new FormTestResult() : FormTestResult::find($result_id)
        ]);
    }

    public function storeResult(Request $request, $id, $result_id)
    {
        $result =  empty($result_id) ? new FormTestResult() : FormTestResult::find($result_id);
        $result->date = Carbon::parse($request->get('date'));
        $result->teacher_id = Auth::user()->id;
        $result->lesson = $request->get('lesson');
        $result->group = $request->get('group');
        $result->result = $request->get('result');
        $result->test_id = $id;
        $result->save();
        return redirect(route('test.show', [$id]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $test = new FormTest();
        $test->name = $request->get('name');
        $test->teacher_id = Auth::user()->id;
        $test->grade = $request->get('grade');
        $test->save();
        return redirect(route('test.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('forms/test/view', [
            'test' => FormTest::find($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('forms/test/form', [
            'action' => route('test.update', [$id]),
            'method' => 'put',
            'test' => FormTest::find($id)
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
        $test =  FormTest::find($id);
        $test->name = $request->get('name');
        $test->teacher_id = Auth::user()->id;
        $test->grade = $request->get('grade');
        $test->save();
        return redirect(route('test.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FormTest::destroy($id);
        return redirect(route('test.index'));
    }

    public function destroyResult($id, $result_id)
    {
        FormTestResult::destroy($result_id);
        return redirect(route('test.show', [$id]));
    }
}
