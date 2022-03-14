<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Custom\Year;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Grade
 *
 * @property int $id
 * @property int|null $year
 * @property string|null $letter
 * @property string|null $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property mixed $number
 * @method static Builder|Grade query()
 * @method static Builder|Grade whereCreatedAt($value)
 * @method static Builder|Grade whereId($value)
 * @method static Builder|Grade whereLetter($value)
 * @method static Builder|Grade whereTitle($value)
 * @method static Builder|Grade whereUpdatedAt($value)
 * @method static Builder|Grade whereYear($value)
 */
class Grade extends Model
{
    protected $table = 'grade';
    protected $highSchoolLimit = 10;

    public const NINTH_GRADE = 9;

    public function getNumberAttribute()
    {
        $currentYear = Year::getInstance()->getYear();
        $number = $currentYear - $this->year + 1;
        return $number;
    }

    /**
     * @param array $ranges
     * @param $grade
     * @return bool
     */
    public static function isGradeInRange($grade, array $ranges): bool
    {
        $grades = [];
        foreach ($ranges as $range) {
            if (is_array($range)) {
                $grades = array_merge($grades, range($range[0], $range[1]));
            } else {
                $grades[] = $range;
            }
        }

        return in_array($grade, $grades, false);
    }

    public static function getActive()
    {
        $currentYear = Year::getInstance()->getYear();

        return self::query()
            ->where('year', '>=', $currentYear - 10)
            ->orderBy('year', 'desc')
            ->orderBy('letter', 'asc')
            ->get();
    }

    public static function getList($filter = null, $hideChildGarden = true, $graduates = false)
    {
        $currentYear = Year::getInstance()->getYear();
        $graduateYear = $currentYear - 11;

        return self::query()
            ->when($graduates, function ($query) use ($graduateYear) {
                /** @var Builder $query */
                $query->where('year', '<=', $graduateYear);
            })
            ->when(!$graduates, function ($query) use ($graduateYear) {
                /** @var Builder $query */
                $query->where('year', '>', $graduateYear);
            })
            ->when($hideChildGarden, function ($query) use ($currentYear) {
                /** @var Builder $query */
                $query->where('year', '<=', $currentYear);
            })
            ->when(!empty($filter), function ($query) use ($filter) {
                /** @var Builder $query */
                $query->whereIn('id', '<=', $filter);
            })
            ->limit(100)
            ->orderBy('grade.year', 'DESC')
            ->orderBy('grade.letter', 'ASC')
            ->get();
    }

    public function getIsHighSchoolAttribute()
    {
        return $this->number >= $this->highSchoolLimit;
    }


    public function getNumberLetterAttribute()
    {
        return $this->number.$this->letter;
    }

    /**
     * Если админ то возвращаем все параллели, если преподаватель то, только его параллели
     * @return Builder[]|Collection
     */
    public static function gradesForRole()
    {
        $grades = self::query()
            ->select('grade.*')
            ->join('schedule', 'schedule.grade_id', '=', 'grade.id')
            ->join('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
            ->groupBy('grade.id')
            ->orderBy('year', 'desc')
            ->orderBy('letter', 'asc');

        if (Auth::user()->role->name == 'admin') {
            return $grades->get();
        }

        return $grades->where('st.teacher_id', Auth::user()->id)->get();
    }
}
