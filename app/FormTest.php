<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTest extends Model
{
    public function teacher() {
        return $this->hasOne('App\User', 'id', 'teacher_id');
    }

    public function results() {
        return $this->hasMany('App\FormTestResult', 'test_id', 'id');
    }
}
