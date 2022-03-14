<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormNote extends Model
{
    public function teacher() {
        return $this->hasOne('App\User', 'id', 'teacher_id');
    }
    public function lesson() {
        return $this->hasOne('App\Lesson', 'id', 'lesson_id');
    }
    public function student() {
        return $this->hasOne('App\User', 'id', 'student_id');
    }
}
