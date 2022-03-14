<?php

namespace App;

use App\Custom\Year;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\FormKrPlan
 *
 * @property-read \App\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormKrPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormKrPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormKrPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormKrPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormKrPlan whereTypeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FormKrPlan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FormKrPlan extends Model
{
    public function lesson() {
        return $this->hasOne('App\Lesson', 'id', 'lesson_id');
    }

    public static function getKr($print, $currentGrade, $month = null)
	{
		return self::query()
			->select('form_kr_plans.*', DB::raw(Year::getInstance()->getYear().' - grade.year + 1 as grade_number' ), 'grade.id as grade_id' )
			->join('grade', 'grade.id', '=', 'grade_id' )
			->when(null === $print, function ($query) use ($currentGrade, $month) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('grade_id', $currentGrade);
				if ($month !== null) {
                    $query->where(DB::raw('month(date)'), $month);
                }
			})
			->get()
			->groupBy(['date', 'grade_id']);
	}

	public static function periodsForKr($month = null)
	{
	    if($month === null) {
            $year = Year::getInstance()->getYear();
            $period = CarbonPeriod::create($year.'-09-01', ($year+1).'-05-30');
        }
	    else {
	        $year = Year::getInstance()->getYear() + intval($month < 9);
            $period = CarbonPeriod::create(
                Carbon::create()->day(1)->month($month)->year($year),
                Carbon::create()->day(1)->month($month)->year($year)->lastOfMonth()
            );
        }

		foreach ($period as $date) {
			$result[$date->year][$date->month][$date->weekOfYear][$date->dayOfWeekIso] = $date;
		}
		return $result??[];
	}

	public static function periodsForKrPrint()
	{
		$period = CarbonPeriod::create('2019-09-01', '2020-05-30');

		foreach ($period as $date) {
			if ($date->dayOfWeekIso < 6) {
				$result[$date->year][$date->weekOfYear][$date->dayOfWeekIso - 1] = $date;
			}
		}
		return $result??[];
	}
}
