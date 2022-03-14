<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'LoginController@index');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/events', 'EventController@index');
Route::get('/events/get-list', 'EventController@getList');
Route::post('/events/set-event', 'EventController@setEvent');
Route::get('/files', 'FilesController@index');
Route::get('/grades', 'GradeController@index');
Route::get('/holiday', 'HolidayController@index');
Route::get('/lessons', 'LessonController@index');
Route::get('/filemanager', 'FilesController@index');
Route::get('/user', 'UserController@index');

Route::resource('students', 'StudentController');
Route::post('/students/{id}/add-comment', 'StudentController@addComment');
Route::post('/students/{id}/add-achievement', 'StudentController@addAchievement');
Route::post('/students/{id}/add-social', 'StudentController@addSocial');
Route::post('/students/edit-social', 'StudentController@editSocial');
Route::post('/students/edit-achievement', 'StudentController@editAchievement');
Route::get('/students/{id}/{layout?}', 'StudentController@show');

Route::resource('teachers', 'TeacherController');
Route::resource('parents', 'ParentController');
Route::get('/filterParent/update', 'ParentController@filterUpdate');
Route::get('/filterStudent/update', 'StudentController@filterUpdate');


// расписание
Route::get('/schedule', 'ScheduleController@index');
Route::get('/schedule/form/{id}', 'ScheduleController@form');
Route::post('/schedule/setLesson', 'ScheduleController@setLesson');
Route::post('/schedule/deleteLesson/{id}', 'ScheduleController@deleteLesson');
Route::post('/schedule/copyLesson/{id}', 'ScheduleController@copyLesson');
Route::post('/schedule/moveLesson/{id}', 'ScheduleController@moveLesson');


Route::resource('krs', 'KRPlanController');


// дневник
Route::get('/calendar', 'CalendarController@index');
Route::post('/calendar/getFormData', 'CalendarController@getFormData');
Route::post('/calendar/setHomework', 'CalendarController@setHomework');
Route::post('/calendar/deleteHomework', 'CalendarController@deleteHomework');
Route::post('/calendar/getHomeworkForLesson', 'CalendarController@getHomeworkForLesson');

Route::get('/homework', 'HomeworkController@index');


// новости

Route::resource('news', 'NewsController')->only([
    'index', 'show'
]);;


// группы
Route::resource('groups', 'GroupController');
Route::get('groups/students/{grade_id}/{lesson_id}', 'GroupController@students');
//todo maybe is better to use Route::match
Route::post('groups/students/{grade_id}/{lesson_id}', 'GroupController@students');

Route::get('/plan/upload-file', 'PlanController@uploadFile');
Route::post('/plan/upload-file', 'PlanController@uploadFile');
Route::resource('/plan', 'PlanController');
Route::post('/plan/store', 'PlanController@store');
Route::post('/plan/update/{id}', 'PlanController@update');
Route::post('/plan/upload/{step}', 'PlanController@upload');

// оценки
Route::get('/score/', 'ScoreController@index');
Route::get('/scores/', 'ScoreController@myScore');
Route::get('/score/index/{blade?}', 'ScoreController@index');
Route::get('/score/edit', 'ScoreController@edit');
Route::get('/attendance/edit', 'ScoreController@editAttendance');
Route::get('/filter/update', 'ScoreController@filterUpdate');

Route::post('/score/save', 'ScoreController@save');
Route::post('/score/delete', 'ScoreController@delete');
Route::post('/score/scorePeriodSave', 'ScoreController@scorePeriodSave');
Route::post('/score/deleteScorePeriod', 'ScoreController@deleteScorePeriod');
Route::get('/score/scorePeriodEdit', 'ScoreController@scorePeriodEdit');

// Посещаемость
Route::post('/attendance/save', 'ScoreController@saveAttendance');
Route::post('/attendance/delete', 'ScoreController@deleteAttendance');

Route::get('/attendance-school/', 'AttendanceSchoolController@index');
Route::post('/attendance-school/save', 'AttendanceSchoolController@save');
Route::post('/attendance-school/delete', 'AttendanceSchoolController@delete');
Route::get('/attendance-school/edit', 'AttendanceSchoolController@form');

// Комменты по журналу
Route::post('/schedule-comment/save', 'ScheduleCommentController@save');
Route::get('/schedule-comment/edit', 'ScheduleCommentController@edit');
Route::post('/schedule-comment/delete', 'ScheduleCommentController@delete');

// Дз по журналу
Route::post('/schedule-homework/save', 'ScheduleHomeworkController@save');
Route::post('/schedule-homework/delete', 'ScheduleHomeworkController@delete');

// Формы
Route::get('/forms', 'FormController@index');
Route::resource('/forms/test', 'FormTestController');
Route::get('/forms/test/{id}/result-form/{result_id}', 'FormTestController@editResult');
Route::post('/forms/test/{id}/result-form/{result_id}', 'FormTestController@storeResult')
    ->name('test.storeresult');
Route::delete('/forms/test/{id}/result-form/{result_id}', 'FormTestController@destroyResult')
    ->name('test.destroyresult');
Route::resource('/forms/notes', 'FormNoteController');

Route::resource('/forms/kr-plan', 'FormKRPlanController');
Route::get('/forms/kr-plan-export', 'FormKRPlanController@krPlanExport');


// Отчеты
Route::get('/reports', 'ReportController@index');
Route::get('/reports/homework/{layout?}', 'ReportController@homework');
Route::get('/reports/score/{layout?}', 'ReportController@score');
Route::get('/reports/score-total/{layout?}', 'Report\ScoreTotalController@index');

Route::get('/reports/score-all/{layout?}', 'Report\ScoreAllController@index');

Route::get('/reports/score-all-avg/{layout?}', 'ReportController@scoreAllAvg');
Route::get('/reports/score-avg/{layout?}', 'ReportController@scoreAvg');
Route::get('/reports/attendance-all/{layout?}', 'ReportController@attendanceAll');
Route::get('/reports/attendance/{layout?}', 'Report\AttendanceController@index');
Route::get('/reports/attendance-summary/{layout?}', 'Report\AttendanceSummaryController@index');
Route::get('/reports/attendance-summary-student/{studentId}/{layout?}', 'Report\AttendanceSummaryController@student');


Route::get('/reports/score-school/{layout?}', 'ReportController@scoreSchool');
Route::get('/reports/rating/{layout?}', 'Report\RatingController@index');
Route::get('/reports/class-teacher/{layout?}', 'Report\ClassTeacherController@index');
Route::get('/reports/supervise-teacher-homework/{layout?}', 'ReportController@superviseTeacherHomework');
Route::get('/reports/supervise-log/{layout?}', 'Report\SuperviseLogController@index');


//Отчеты EXCEL
Route::get('/reports/homework-export/{layout?}', 'ReportController@homeworkExport');
Route::get('/reports/score-export/{layout?}', 'ReportController@scoreExport');
Route::get('/reports/score-total-export/{layout?}', 'Report\ScoreTotalController@excel');

Route::get('/reports/rating-export', 'Report\RatingController@excel');
Route::get('/reports/attendance-export', 'Report\AttendanceController@excel');

Route::get('/reports/score-all-export/{layout?}', 'Report\ScoreAllController@excel');
Route::get('/reports/score-all-avg-export/{layout?}', 'ReportController@scoreAllAvgExport');

Route::get('/reports/score-avg-export/{layout?}', 'ReportController@scoreAvgExport');
Route::get('/reports/attendance-all-export/{layout?}', 'ReportController@attendanceAllExport');
Route::get('/reports/score-school-export/{layout?}', 'ReportController@scoreSchoolExport');
Route::get('/reports/class-teacher-export/{layout?}', 'Report\ClassTeacherController@excel');

// Контент
Route::get('/content/{slug}', 'ContentController@show');

Route::get('/help', 'ContentController@help');

// Письма
Route::get('/sendmail/invitation/{userId}', 'EmailController@sendInvitation');
Route::get('/sendmail/score/{userId}', 'EmailController@sendScore');
Route::get('/sendmail/score-total/{userId}', 'EmailController@sendScoreTotal');

Route::get('/sendmail/invitation/{gradeId}/{role}', 'EmailController@sendInvitationGrade');
Route::get('/sendmail/score/{gradeId}', 'EmailController@sendScoreGrade');

//Чат
Route::get('/messenger', 'MessengerController@index');
Route::get('/messenger/getContacts', 'MessengerController@getContacts');
Route::get('/messenger/getMessages', 'MessengerController@getMessages');
Route::get('/messenger/viewMessages', 'MessengerController@viewMessages');
Route::get('/messenger/sendMessage', 'MessengerController@sendMessage');
Route::post('/messenger/uploadFile', 'MessengerController@uploadFile');
Route::get('/messenger/getNewMessages', 'MessengerController@getNewMessages');

// Опросы
Route::post('/poll/{id}/save-result', 'PollController@saveResult');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
