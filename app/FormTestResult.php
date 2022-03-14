<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTestResult extends Model
{
    public function teacher() {
        return $this->hasOne('App\User', 'id', 'teacher_id');
    }
}
