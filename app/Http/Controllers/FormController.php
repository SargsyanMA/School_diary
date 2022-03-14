<?php

namespace App\Http\Controllers;

use App\Holiday;
use Illuminate\Http\Request;

class FormController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('forms.index', [
            'title' => 'Отчеты'
        ]);
    }

}
