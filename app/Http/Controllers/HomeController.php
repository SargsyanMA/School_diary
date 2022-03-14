<?php

namespace App\Http\Controllers;

use App\Event;
use App\Grade;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 */
    public function __construct()
    {
        $this->middleware('auth');
    }

	/**
	 * Show the application dashboard.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\Support\Renderable
	 */

    public function index(Request $request)
    {
        $events = Event::all();
        $grades = Grade::getActive();

        $news = News::query()
            ->orderBy('date', 'desc')
            ->get();

        return view('events', [
            'title'=>'Лента',
            'events'=>$events,
            'grades'=>$grades,
            'can_edit' => Auth::user()->role->name == 'admin' || Auth::user()->admin,
            'news' => $news
        ]);

    }
}
