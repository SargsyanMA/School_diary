<?php

namespace App\Http\Controllers;

use App\Content;
use App\Custom\Teacher;
use App\Homework;
use App\Grade;
use App\Schedule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, $slug)
    {
        $content = Content::where('slug', $slug)->first();

        return view('content.show', [
            'html' => $content->html,
            'html_teacher' => $content->html_teacher,
            'title' => $content->title,
            'show_teacher' => in_array($request->user()->role->name,   ['admin', 'teacher', 'curator'])
        ]);
    }

    public function help()
    {
        return view('content.help', [
            'title' => 'Инструкция',
        ]);
    }
}
