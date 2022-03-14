<?php

namespace App\Console\Commands;

use App\Custom\Period;
use App\Custom\Report;
use App\Custom\Year;
use App\Mail\MailScores;
use App\ScheduleHomework;
use App\Score;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use \Mail;

class SendMailsScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendMails:scores {--grade=} {--total=}  {--id=} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send mails with scores';

    /**
     * Create a new command instance.
	 */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $studentId = $this->option('id');
        $total = $this->option('total');
        $email =  $this->option('email');
        $gradeId = $this->option('grade');

		$students = User::query()
			->where('role_id', 2)
            ->when($studentId, function ($query) use ($studentId) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('id', $studentId);
            })
            ->when($gradeId, function ($query) use ($gradeId) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('class', $gradeId);
            })
			->whereNotNull('class')
			->orderBy('class', 'ASC')
			->orderBy('name', 'ASC')
			->get();

		foreach ($students as $s) {
			if (isset($s->grade->number)) {
				$parents = [];
				Period::firstDaysPeriod($s->grade->number);
				Period::lastDaysPeriod($s->grade->number);
				$period = Period::definePeriod(Carbon::now()->timestamp, $s->grade->number);


                $date = [
                    'value' => [
                        Carbon::parse(Period::$firstDays[$period-1])->format('d.m.Y'),
                        Carbon::now()->format('d.m.Y')
                    ]
                ];

				$filter = [

                    'period' => [
                        'value' => $s->grade->isHighSchool ? 'p'.$period : 'ch'.$period,
                        'name_field' => 'number'
                    ],
					'student_id' => [
						'value' => $s->id,
						'name_field' => 'name'
					],
                    'grade_id' => [
                        'value' => $s->class,
                        'name_field' => 'number'
                    ],
                    'year' => [
                        'value' => Year::getInstance()->getYear(),
                        'name_field' => 'number'
                    ]
				];

				//dd($filter);

				$data = [
					'scores' => Report::searchResult($filter),
					'attendance' => Report::attendanceStudent($filter),
					'weightedAverage' => Report::weightedAverageScore($filter),
                    'totalAverage' => Report::weightedAverageScore($filter, null)[0],
                    'scorePeriod' => $s->scorePeriod->groupby(['period_number', 'lesson_id']),
                    'schedule' => null !== $s ? Score::studentSchedule($s, $filter['year']['value']) : [],
					'student' => $s,
					'date' => [
						Carbon::parse($date['value'][0])->format('Y-m-d'),
						Carbon::parse($date['value'][1])->format('Y-m-d')
					],
                    'total' => $total,
                    'period'=> $period
				];

                $filter['period'] = [
                    'value' => Period::defineKeyByGrade($s->grade->number)
                ];

                $data['homeworks'] = ScheduleHomework::rowsByStudentAndPeriod($s->id, $filter);

				//dd($data);

				foreach ($s->parents as $p) {
					$parents [] = $p->user->email;
				}

				$curators = User::query()
                    ->where('curator' ,1)
                    ->where('class' ,$s->class)
                    ->get();

				if(!empty($email)) {
                    Mail::to($email)->send(new MailScores($data));
                }
				else {
                    Mail::to($parents)->send(new MailScores($data));
                    Mail::to($s)->send(new MailScores($data));
                    Mail::to($curators)->send(new MailScores($data));
                    Mail::to('uncle.slavik@gmail.com')->send(new MailScores($data));

                }
			}
		}
    }
}
