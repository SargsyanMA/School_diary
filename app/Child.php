<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Child
 *
 * @property int $id
 * @property int|null $parent
 * @property string $name
 * @property string|null $phone
 * @property string|null $birthDate
 * @property int $grade
 * @property string|null $relation
 * @property string|null $notes
 * @property string|null $email
 * @property string $password
 * @property string $password_clean
 * @property string|null $hash
 * @property string|null $last_auth
 * @property string|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereLastAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child wherePasswordClean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Child whereRelation($value)
 * @mixin \Eloquent
 */
class Child extends Model
{
    protected $table = 'child';

}
