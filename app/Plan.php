<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use \PhpOffice\PhpSpreadsheet\Reader\Xls;
use \PhpOffice\PhpSpreadsheet\Reader\Exception as PhpOfficeException;
use \Illuminate\Database\Eloquent\Builder;

/**
 * App\Plan
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $lesson_num
 * @property int $grade_num
 * @property int $teacher_id
 * @property int $group_id
 * @property string $grade_letter
 * @property string $title
 * @property string|null $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Lesson $lesson
 * @method static Builder|Plan query()
 * @method static Builder|Plan whereComment($value)
 * @method static Builder|Plan whereCreatedAt($value)
 * @method static Builder|Plan whereGradeNum($value)
 * @method static Builder|Plan whereId($value)
 * @method static Builder|Plan whereLessonId($value)
 * @method static Builder|Plan whereLessonNum($value)
 * @method static Builder|Plan whereTeacherId($value)
 * @method static Builder|Plan whereTitle($value)
 * @method static Builder|Plan whereUpdatedAt($value)
 */
class Plan extends Model
{
    protected $table = 'lesson_plan';


    public function lesson()
    {
        return $this->hasOne('App\Lesson', 'id', 'lesson_id');
    }

    /**
     * Парсим и сохраняем файл эксел с планом
     * @param $request
     * @return bool
     * @throws PhpOfficeException
     */
    public static function loadPlanFromFile($request): void
    {
        self::saveParsedFile($request, self::parsePlanFile($request));
    }

    /**
     * Парсим эксел
     * @param $request
     * @return array
     * @throws PhpOfficeException
     */
    public static function parsePlanFile($request): array
    {
        $file = $request->file('plan');

        //$reader = new Xls();
        $inputFileType = \PHPExcel_IOFactory::identify($file);
        $reader =\PHPExcel_IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getPathname());

        return $spreadsheet->getWorksheetIterator()->current()->toArray();
    }

    /**
     * Сохраняем строки из эксел файла
     * @param $request
     * @param $rows
     * @return void
     */
    public static function saveParsedFile($request, $rows): void
    {

        //dd($rows);
        if (!empty($rows)) {
            $gradeNum = (int)$request->get('grade_num');
            $lessonId = (int)$request->get('lesson_id');
            $gradeLetter = $request->get('grade_letter') ? $request->get('grade_letter') : null;
            $groupId = $request->get('group_id') ? $request->get('group_id') : null;
            foreach ($rows as $row) {
                if (!empty($row[0]) && !empty($row[1]) && is_numeric($row[0])) {
                    $plan = new self();

                    $plan->lesson_id = $lessonId;
                    $plan->lesson_num = (int)$row[0];
                    $plan->grade_num = $gradeNum;
                    $plan->teacher_id = Auth::user()->id;
                    $plan->title = $row[1];
                    $plan->grade_letter = $gradeLetter;
                    $plan->group_id = $groupId;
                    $plan->save();
                }
            }
        }
    }

}
