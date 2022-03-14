<?php

namespace App;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Holiday
 *
 * @property int $id
 * @property int|null $year
 * @property string|null $type
 * @property int|null $period_type
 * @property string|null $begin
 * @property string|null $end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereBegin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday wherePeriodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Holiday whereYear($value)
 * @mixin \Eloquent
 */
class Holiday extends Model
{
    protected $table = 'holiday';

	public static function getHolidays($year, $dateRange = null) {

		return self::where('year', $year)
			->where('period_type', 1)
			->when($dateRange, function ($query) use ($dateRange) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('begin', '>=', Carbon::parse($dateRange[0])->toDateString());
				$query->where('end', '<=', Carbon::parse($dateRange[1])->toDateString());
			})
			->get();
	}

	/**
	 * @param $year
	 * @return Carbon[]
	 */
	public static function getHolidaysDaysArray($year) {
		$holidays = Holiday::getHolidays($year);
		$days = [];
		foreach ($holidays as $holiday) {
			$days = array_merge($days, CarbonPeriod::create($holiday->begin, $holiday->end)->toArray());
		}
		return $days;
	}

}
