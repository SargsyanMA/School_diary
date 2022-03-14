<?php

namespace App\Http\Controllers;

use App\Custom\Year;
use App\Holiday;

class HolidayController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $types = [
            'autumn' => 'Осенние',
            'winter' => 'Зимние',
            'spring' => 'Весенние',
            'may' => 'Майские',
            'summer' => 'Летние'
        ];

        return view('holiday', [
            'title'=>'Каникулы',
            'holiday'=> Holiday::where('year', Year::getInstance()->getYear())->get(),
            'types'=>$types,
        ]);
    }

    public function getList() {
        return  Holiday::query()
            ->where('year', Year::getInstance()->getYear())
            ->where('period_type', 1)
            ->get();
    }

}
