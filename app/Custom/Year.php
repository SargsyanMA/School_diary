<?php
namespace App\Custom;

use App\Grade;
use Illuminate\Support\Facades\Cookie;

class Year {


    private $firstYear = 2018;
    private $year;

    private static $_instance = null;

    private function __construct()
    {
        $this->year = Cookie::get('year', $this->year);
    }

    protected function __clone()
    {
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getYear() {

        $defaultYear = $this->getDefaultYear();

        if ($this->userCanChangeYear() && $this->year>=$this->firstYear  ) {
            return  (int)$this->year;
        }
        else {
            return (int)$defaultYear;
        }
    }

    public function getYearEnd() {
        $year = $this->getYear();
        return '30.06.'.($year+1);
    }
    public function getYearBegin() {
        $year = $this->getYear();
        return '01.09.'.$year;
    }

    public function getYearList() {
        $currentYear = (int)date('Y');
        $years = range($this->firstYear,$currentYear);
        return $years;
    }

    private function getDefaultYear() {
        $month=date('n');

        if ($month>=8) {
            $defaultYear=date('Y');
        }
        else {
            $defaultYear=date('Y')-1;
        }

        return (int)$defaultYear;
    }

    public function userCanChangeYear() {
        return true; //Auth::user()->role->name == 'admin'; // todo
    }

    public function setYear($year) {
        $this->year = (int)$year;
        setcookie('year',$this->year,null,'/');
    }

    public function gradeIdFromClass($class) {
		$currentYear = Year::getInstance()->getYear();
		$number = ($currentYear +1 ) - $class;
		$grade = Grade::where('year', $number)->first();
		return $grade->id;
	}

	public function getGradeNumberByYear($year) {
		$currentYear = self::getInstance()->getYear();
		$number = $currentYear - $year + 1;

/*		if ($number<1) {
			return $this->childGardenGroups[$number];
		}*/

		return $number;
	}

}
