<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StudentSocial
 *
 * @property int $id
 * @property int $student_id
 * @property string $value
 * @property string $date
 * @property string $comment
 * @mixin \Eloquent
 */
class StudentSocial extends Model
{
    protected $table = 'student_social';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'student_id', 'comment', 'value'
	];

	public const SOCIAL_SCORES = [-3, -2, -1, 1, 2, 3];
}
