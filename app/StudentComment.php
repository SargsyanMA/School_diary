<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StudentComment
 *
 * @property int $id
 * @property int $student_id
 * @property int $author_id
 * @property string|null $text
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $author
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentComment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentComment extends Model
{
    protected $table = 'student_comment';

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }
}
