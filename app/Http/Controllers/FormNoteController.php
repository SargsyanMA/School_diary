<?php

namespace App\Http\Controllers;

use App\FormNote;
use App\FormTest;
use App\Lesson;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('forms/notes/index', [
            'notes' => FormNote::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forms/notes/form', [
            'action' => route('notes.store'),
            'note' => new FormNote(),
            'method' => 'post',
            'students' => User::query()
                ->where('role_id', 2)
                ->orderBy('name')
                ->get(),
            'lessons' => Lesson::all()
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
        $note = new FormNote();
        $note->student_id = $request->get('student_id');
        $note->teacher_id = Auth::user()->id;
        $note->lesson_id = $request->get('lesson_id');
        $note->note = $request->get('note');
        $note->solve = $request->get('solve');
        $note->recommend = $request->get('recommend');
        $note->save();
        return redirect(route('notes.index'));
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
        return view('forms/notes/form', [
            'action' => route('notes.update', [$id]),
            'method' => 'put',
            'note' =>  FormNote::find($id),
            'students' => User::query()
                ->where('role_id', 2)
                ->orderBy('name')
                ->get(),
            'lessons' => Lesson::all()
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
        $note =  FormNote::find($id);
        $note->student_id = $request->get('student_id');
        $note->teacher_id = Auth::user()->id;
        $note->lesson_id = $request->get('lesson_id');
        $note->note = $request->get('note');
        $note->solve = $request->get('solve');
        $note->recommend = $request->get('recommend');
        $note->save();
        return redirect(route('notes.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FormNote::destroy($id);
        return redirect(route('notes.index'));
    }
}
