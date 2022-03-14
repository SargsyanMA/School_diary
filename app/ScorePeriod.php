<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ScorePeriod
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $grade_id
 * @property string $grade_letter
 * @property int $group_id
 * @property int $student_id
 * @property string $value
 * @property string $type
 * @property string $comment
 * @property string $period_type
 * @property int $teacher_id
 * @property int $period_number
 * @property string $date
 * @property \App\User $student
 *
 * @method static Builder|ScorePeriod query()
 * @method Builder|ScorePeriod delete()
 * @method Builder|ScorePeriod first()
 * @method static Builder|ScorePeriod find($value)
 * @method static Builder|ScorePeriod whereCode($value)
 * @method static Builder|ScorePeriod whereCreatedAt($value)
 * @method static Builder|ScorePeriod whereId($value)
 * @method static Builder|ScorePeriod whereName($value)
 * @method static Builder|ScorePeriod whereUpdatedAt($value)
 */
class ScorePeriod extends Model
{
	public const TOTAL_TYPE = 5;
    public const EXAM_TYPE = 6;
    public const ATT_TYPE = 7;

    protected $table = 'score_period';

	public function student()
    {
		return $this->hasOne('App\User', 'id', 'student_id');
	}

    public function type()
    {
        return $this->hasOne('App\ScoreType', 'id', 'type_id');
    }

	/**
	 * returns the period type: 'half' for 10th and 11th grades and 'quarter' for the rest
	 * @param $classNumber
	 * @return string
	 */
	public static function getPeriodTypeByClassNumber($classNumber): string
    {
		if (Grade::NINTH_GRADE < $classNumber) {
			return 'half';
		}
		return 'quarter';
	}

}
