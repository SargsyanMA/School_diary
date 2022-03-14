<?php

namespace App;

use App\Custom\Year;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Input;

/**
 * App\User
 *
 * @property int $id
 * @property int|null $role_id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $active
 * @property string|null $position
 * @property string|null $class
 * @property string|null $lesson
 * @property string|null $phone
 * @property string|null $contacts
 * @property string|null $contacts2
 * @property int|null $group
 * @property string|null $photo
 * @property string|null $relation
 * @property string|null $birthdate
 * @property string|null $passwordClean
 * @property string|null $passwordRecovery
 * @property string|null $passwordRecoveryExpire
 * @property string|null $lastAuthorization
 * @property string|null $login
 * @property int|null $child_id
 * @property int|null $parent_id
 * @property string|null $note
 * @property string|null $class_letter
 * @property string|null $curator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\StudentComment[] $comments
 * @property mixed $locale
 * @property mixed $short_name
 * @property \App\Grade $grade
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \App\User $parent
 * @property \App\StudentParent[] $parents
 * @property \App\StudentParent[] $students
 * @property \App\StudentParent[] $children
 * @property \TCG\Voyager\Models\Role|null $role
 * @property \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Role[] $roles
 * @property \Illuminate\Database\Eloquent\Collection|\App\Score[] $score
 * @property \Illuminate\Database\Eloquent\Collection|\App\Attendance[] $attendance
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereClassLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereContacts2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastAuthorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLesson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePasswordClean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePasswordRecovery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePasswordRecoveryExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRelation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

	public const STUDENT = 2;
	public const TEACHER = 3;
	public const PARENT = 4;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'passwordClean'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	public static function ratingReportQuery() {

	    $gradeId = Input::get('grade_id');
		return User::query()
			->select([
				'users.*',
				DB::raw('sum(score.value*score_types.weight)/sum(score_types.weight) as score'),
				DB::raw('sum(student_social.value) as social'),
                DB::raw('ifnull(sum(score.value*score_types.weight)/sum(score_types.weight),0) + ifnull(sum(student_social.value),0) as total')
			])
			->leftJoin('score', 'score.student_id', '=', 'users.id')
			->leftJoin('student_social', 'student_social.student_id', '=', 'users.id')
			->leftJoin('score_types', 'score_types.id', '=', 'score.type_id')
			->where('role_id', 2)
            ->when($gradeId, function ($query) use ($gradeId) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('users.class', $gradeId);
            })
			->groupBy('users.id')
			->orderBy('total', 'desc')
			->get();
	}

    public function getShortNameAttribute() {
        $arName=explode(' ',trim($this->name));
        return "{$arName[0]} {$arName[1]}";
    }

    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'class');
    }

    public function parent()
    {
        return $this->hasOne('App\User', 'id', 'parent_id');
    }

	public function parents()
	{
		return $this->hasMany('App\StudentParent', 'student_id', 'id');
	}

	public function students()
	{
		return $this->hasMany('App\StudentParent', 'parent_id', 'id');
	}

	public function group()
	{
		return $this->hasOne('App\UserGroup', 'user_id', 'parent_id');
	}

    public function comments()
    {
        return $this->hasMany('App\StudentComment', 'student_id', 'id');
    }

	public function score()
	{
		return $this->hasMany('App\Score', 'student_id', 'id');
	}

    public function scorePeriod()
    {
        return $this->hasMany('App\ScorePeriod', 'student_id', 'id')
            ->where('date', '>=', Carbon::parse(Year::getInstance()->getYearBegin()));
    }

	public function children() {
		return $this->hasMany('App\StudentParent', 'parent_id', 'id');
	}

	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPasswordNotification($token));
	}

	public static function getUserById($id) {
		return self::find($id);
	}



}
