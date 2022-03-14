<?php

namespace App\Http\Controllers;

use App\Event;
use App\Grade;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;


class EmailController extends Controller
{
    public function sendInvitation($userId = null) {
        $user = User::find($userId);
        Artisan::call("sendMails:invitation {$user->role->name} --id={$userId}");
    }

    public function sendInvitationGrade($gradeId, $role = 'parent') {
        Artisan::call("sendMails:invitation {$role} --grade={$gradeId}");
    }

    public function sendScore($userId = null) {
        Artisan::call("sendMails:scores --id={$userId}");
    }
    public function sendScoreTotal($userId = null) {
        Artisan::call("sendMails:scores --total=1 --id={$userId}");
    }

    public function sendGrade($gradeId) {
        Artisan::call("sendMails:scores --id={$gradeId}");
    }
}
