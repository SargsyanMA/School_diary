<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RatingExport implements FromView
{

    private $rating;

    public function __construct($rating)
    {
        $this->rating = $rating;
    }

    public function view(): View
	{
		return view('report.rating-table', [
			'students' => $this->rating
        ]);
	}
}
