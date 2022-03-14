<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\StudentParent
 *
 * @property int $student_id
 * @property int $parent_id
 * @property User $user
 * @property User $student
 *
 * @method \Illuminate\Database\Eloquent\Builder|\App\StudentParent delete()
 * @method \Illuminate\Database\Eloquent\Builder|\App\StudentParent insert($value)
 * @method \Illuminate\Database\Eloquent\Builder|\App\StudentParent where($value,$value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent find($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentParent whereUpdatedAt($value)
 */

class StudentParent extends Model
{
	protected $table = 'students_parents';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'student_id', 'parent_id'
	];

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'parent_id');
	}

	public function student()
	{
		return $this->hasOne('App\User', 'id', 'student_id');
	}

	public static function deleteByStudentId($parents = [], $id) {
		StudentParent::where('student_id',$id)->delete();
		$data = [];
		foreach ($parents as $p) {
			$data[] = [
				'student_id' => $id,
				'parent_id' => $p
			];
		}
		StudentParent::insert($data);
	}

	public static function getStudentsId() {
		$myStudents = Auth::user()->students;
		$myStudentsId = [];
		foreach ($myStudents as $my) {
			/** @var $my StudentParent */
			$myStudentsId[] = $my->student->id;
		}
		return $myStudentsId;
	}
}
