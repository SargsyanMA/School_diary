<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class FilesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        Cookie::queue('userId', Auth::user()->id,100*60*24);

        return view('files', [
            'title' => 'Мои файлы',
        ]);
    }
}
