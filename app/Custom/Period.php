<?php

namespace App\Custom;

use App\Grade;
use App\Holiday;
use App\Schedule;
use Carbon\Carbon;


class Period
{
	public const PERIOD_HALF_YEAR = 3;
	public const PERIOD_QUARTER = 2;

	public static $lastDays;

	public static $firstDays;

	//@todo класс слишком большой
	//@todo в спрвочник в БД
    public const CH1 = 'ch1';
    public const CH2 = 'ch2';
    public const CH3 = 'ch3';
    public const CH4 = 'ch4';
    public const P1 = 'p1';
    public const P2 = 'p2';
    public const YEAR = 'year';
    public const EXAM = 'exam';
    public const TOTAL = 'total';

	public static $periodNames = [
		self::CH1 => 'Ч1',
		self::CH2 => 'Ч2',
		self::CH3 => 'Ч3',
		self::CH4 => 'Ч4',
		self::P1 => 'П1',
		self::P2 => 'П2',
		self::YEAR => 'Г',
		self::EXAM => 'Э',
		self::TOTAL => 'И'
	];

    public static $periodNumbers = [
        self::CH1 => 1,
        self::CH2 => 2,
        self::CH3 => 3,
        self::CH4 => 4,
        self::P1 => 1,
        self::P2 => 2,
        self::YEAR => 5,
        self::EXAM => 6,
        self::TOTAL => 7
    ];

	public static $periodNamesRaw = [
		'1Ч',
		'2Ч',
		'3Ч',
		'4Ч'
	];

    public static $periodNamesRawHalf = [
        '1П',
        '2П'
    ];

	/**
	 * очищаем массив с названиями периодов
	 * @param int $grade
	 * @param bool $onlyPeriods
	 * @param bool $showYear
	 */
	public static function cutPeriodNames($grade, $onlyPeriods = false, $showYear = true): void
    {
		if (null !== $grade) {
			if ($grade > Grade::NINTH_GRADE) {
				unset(
					self::$periodNames[self::CH1],
					self::$periodNames[self::CH2],
					self::$periodNames[self::CH3],
					self::$periodNames[self::CH4]
				);
			} else {
				unset(
					self::$periodNames[self::P1],
					self::$periodNames[self::P2]
				);
			}
		}

		if (!$showYear) {
			unset(self::$periodNames[self::YEAR]);
		}

		if ($onlyPeriods) {
			unset(
				self::$periodNames[self::EXAM],
				self::$periodNames[self::TOTAL]
			);
		}
	}

	/**
	 * Определяет первый и последный день периода
	 * @param array $filter
	 * @return array
	 */
	public static function defineFirstAndLastDays(array $filter): array
    {



		$firstDay = '-';
		$lastDay = '-';
		if (preg_match('/^ch\d/', $filter['period']['value'])) {
			$part = (int) filter_var($filter['period']['value'], FILTER_SANITIZE_NUMBER_INT);
			self::firstDaysPeriod(1, true,$filter['year']['value'] ?? Year::getInstance()->getYear());
			self::lastDaysPeriod(1, true,$filter['year']['value'] ?? Year::getInstance()->getYear());
			$firstDay = Carbon::parse(self::$firstDays[$part-1])->format('Y-m-d');
			$lastDay = Carbon::parse(self::$lastDays[$part-1])->format('Y-m-d');
		} elseif (preg_match('/^p\d/', $filter['period']['value'])) {
			$part = (int) filter_var($filter['period']['value'], FILTER_SANITIZE_NUMBER_INT);
			self::firstDaysPeriod(11,  true,$filter['year']['value'] ?? Year::getInstance()->getYear());
			self::lastDaysPeriod(11, true,$filter['year']['value'] ?? Year::getInstance()->getYear());
			$firstDay = Carbon::parse(self::$firstDays[$part-1])->format('Y-m-d');
			$lastDay = Carbon::parse(self::$lastDays[$part-1])->format('Y-m-d');
		} elseif (preg_match('/year/', $filter['period']['value'])) {
			$firstDay = Carbon::parse(Year::getInstance()->getYearBegin())->format('Y-m-d');
			$lastDay = Carbon::parse(Year::getInstance()->getYearEnd())->format('Y-m-d');
		}

		return [$firstDay, $lastDay];
	}

    /**
     * вернет либо дата начала и конца периода, либо весь год (если не найдет периода)
     * @param array $filter
     * @return array
     */
	public static function getPeriodOrAllYear(array $filter): array
    {
        $period = self::defineFirstAndLastDays($filter);
        if (in_array('-', $period, true)) {
            return Schedule::getAllYearDateRange();
        }
        return [Carbon::parse($period[0]), Carbon::parse($period[1])];
    }

	public static function firstDayOfPeriod($grade, $date = false)
    {
        $date = $date ? Carbon::now()->timestamp : $date;
		self::firstDaysPeriod($grade);
		self::lastDaysPeriod($grade);
		$period = self::definePeriod($date, $grade);
		return self::$firstDays[$period - 1];
	}

	public static function lastDayOfPeriod($grade, $date = false)
    {
		$date = $date ? Carbon::now()->timestamp : $date;
		self::firstDaysPeriod($grade);
		self::lastDaysPeriod($grade);
		$period = self::definePeriod($date, $grade);
		return self::$lastDays[$period - 1];
	}

	/**
	 * выбирает те названий, которые нам надо отображать
	 * @param $grade
	 * @return void
	 */
	public static function periodNames($grade): void
    {
		$period = self::definePeriod(time(), $grade);
		self::$periodNames = array_slice(self::$periodNames, 0, $period);
	}

	public static function definePeriodKey()
    {
		return [
			'smallClass' => self::definePeriod(Carbon::now()->timestamp, 1, self::lastDaysPeriod(1, false)),
			'bigClass' => self::definePeriod(Carbon::now()->timestamp, 11, self::lastDaysPeriod(11, false))
		];
	}

	public static function defineKeyByGrade($grade)
    {
		$keys = self::definePeriodKey();
		if (Grade::NINTH_GRADE < $grade) {
			return 'p'.$keys['bigClass'];
		}
		return 'ch'.$keys['smallClass'];
	}

	/**
	 * определяет на каком периоде мы сейчас
	 * @param int $date has to be timestamp
	 * @param int $grade
	 * @param array $lastDays
	 * @return int
	 */
	public static function definePeriod($date, $grade, $lastDays = [])
    {
		$lastDays = empty($lastDays)?self::$lastDays:$lastDays;
        //если занятия закончились то мы считаем что у нас 4/2 период
		$period = $grade > Grade::NINTH_GRADE? 2 : 4;
		foreach ($lastDays as $k => $p) {
			if ($date < $p) {
				$period = $k+1;
				break;
			}
		}
		return $period;
	}

	/**
	 * Give as the first day of an array of periods (quarters or half-years)
	 * @param int $grade
	 * @param bool $modifyProperty
	 * @return array
	 */
	public static function firstDaysPeriod($grade, $modifyProperty = true, $year=null): array
    {
		$period_type = self::PERIOD_QUARTER;
		if ($grade > Grade::NINTH_GRADE) {
			$period_type = self::PERIOD_HALF_YEAR;
		}

		if($year) {
            Year::getInstance()->setYear($year);
        }

		$periods = Holiday::query()
			->where('year', Year::getInstance()->getYear())
			->where('period_type', $period_type)
			->get()->toArray();

		$firstDays = array_map('strtotime', array_column($periods, 'begin'));
		if ($modifyProperty) {
			self::$firstDays = $firstDays;
		}

		return $firstDays;
	}

	/**
	 * Give as the last day of an array of periods (quarters or half-years)
	 * @param $gradeNumber
	 * @param bool $modifyProperty
	 * @return array
	 * @internal param int $grade
	 */
	public static function lastDaysPeriod($gradeNumber, $modifyProperty = true, $year=null): array
    {
		$period_type = self::PERIOD_QUARTER;
		if ($gradeNumber > Grade::NINTH_GRADE) {
			$period_type = self::PERIOD_HALF_YEAR;
		}

        if($year) {
            Year::getInstance()->setYear($year);
        }

		$periods = Holiday::query()
			->where('year', Year::getInstance()->getYear())
			->where('period_type', $period_type)
			->get()->toArray();

		$lastDays = array_map('strtotime', array_column($periods, 'end'));
		if ($modifyProperty) {
			self::$lastDays = $lastDays;
		}

		return $lastDays;
	}

}
