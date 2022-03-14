<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\LessonType;

class LessonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('lessons', [
            'title'=>'Уроки',
            'lessons' => Lesson::all(),
            'types' => LessonType::all()

        ]);
    }


}
