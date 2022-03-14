<?php

namespace App\Custom;

use App\Attendance;
use App\Grade;
use App\Lesson;
use App\Schedule;
use App\Score;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection as CollectionDb;

class Report
    {
	public const SCHOOL_REPORT = [
		5 => null,
		6 => null,
		7 => null,
		8 => null,
		58 => null,
		9 => null,
		10 => null,
		11 => null,
		911 => null,
		'total' =>[
			'quantity' => 0,
			'type' => [
				'perfect' => 0,
				'onePerfect' => 0,
				'perfectGood' => 0,
				'oneGood' => 0,
				'oneRegular' => 0,
				'normal' => 0,
				'badOne' => 0,
				'badTwo' => 0,
				'badMore' => 0
			]
		]
	];

	/**
	 * создаем фильтр
	 * @param Request $request
	 * @param array $fields
	 * @return array
	 */
	public static function createFilter(Request $request, $fields = []): array {

        $grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));
        $periodType = $grade->isHighSchool ? 'half' : 'quarter';

		$filter = [
			'date' => [
				'title' => 'Дата',
				'type' => 'date-range',
				'value' => $request->get('date', [
					Carbon::parse(Year::getInstance()->getYearBegin())->format('d.m.Y'),
					Carbon::now()->format('d.m.Y')
				]),
                'multiple' => false
			],
            'date_single' => [
                'title' => 'Дата',
                'type' => 'date',
                'value' => $request->get('date_single', Carbon::now()->format('d.m.Y')),
                'multiple' => false
            ],
			'teacher_id' => [
				'title' => 'Учитель',
				'type' => 'select',
				'options' =>  User::query()
                    ->select('users.*')
                    ->join('schedule', 'schedule.teacher_id', '=', 'users.id')
                    ->whereIn('role_id', [1,3])
                    ->orderBy('users.name', 'asc')
                    ->groupBy('users.id')
                    ->get(),
				'value' => $request->get('teacher_id'),
				'name_field' => 'name',
                'multiple' => false
			],
			'lesson_id' => [
				'title' => 'Предмет',
				'type' => 'select',
				'options' => Lesson::orderBy('name', 'ASC')->get(),
				'value' => $request->get('lesson_id'),
				'name_field' => 'name',
                'multiple' => false
			],
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Grade::getActive(),
				'value' => $request->get('grade_id', Grade::getActive()->first()->id),
				'name_field' => 'numberLetter',
                'multiple' => false
			],
            'grade_letter' => [
                'title' => 'Класс',
                'type' => 'select',
                'options' => [
                    ['id'=> 'А', 'name'=> 'А'],
                    ['id'=> 'Б', 'name'=> 'Б'],
                    ['id'=> 'В', 'name'=> 'В']
                ],
                'value' => $request->get('grade_letter', null),
                'name_field' => 'name',
                'multiple' => false
            ],
			'student_id' => [
				'title' => 'Ученик',
				'type' => 'select',
				'options' => User::query()
                    ->where('role_id', 2)
                    ->when($request->get('grade_id'), function ($query) use ($request) {
						/** @var Builder $query */
						$query->where('class', $request->get('grade_id'));
                    })
                    ->when($request->get('grade_letter'), function ($query) use ($request) {
                        /** @var Builder $query */
                        $query->where('class_letter', $request->get('grade_letter'));
                    })
                    ->orderBy('name', 'ASC')
                    ->get(),
				'value' => $request->get('student_id'),
				'name_field' => 'name',
                'multiple' => false
			],
            'period' => [
                'title' => 'Период',
                'type' => 'select',
                'options' => array_map(
                    function ($k, $v) {
                        return ['id'=> $k, 'name'=> $v];
                    },
                    array_keys(Period::$periodNames),
                    Period::$periodNames
                ),
                'value' => $request->get('period', Period::CH1),
                'name_field' => 'name',
                'multiple' => false
            ],
            'year' => [
                'title' => 'Год',
                'type' => 'select',
                'options' => [
                    ['id'=> 2019, 'name'=> 2019],
                    ['id'=> 2020, 'name'=> 2020],
                ],
                'value' => $request->get('year', Year::getInstance()->getYear()),
                'name_field' => 'name',
                'multiple' => false
            ],
            'period[]' => [
                'title' => 'Период',
                'type' => 'select',
                'options' => array_map(
                    function ($k, $v) {
                        return ['id'=> $k, 'name'=> $v];
                    },
                    array_keys(Period::$periodNames),
                    Period::$periodNames
                ),
                'value' => $request->get('period',[1]),
                'name_field' => 'name',
                'multiple' => true
            ],
		];

		if(!empty($fields)) {
            $filter = array_filter(
                $filter,
                function ($key) use ($fields) {
                    return in_array($key, $fields);
                },
                ARRAY_FILTER_USE_KEY
            );
        }
		return $filter;
	}

	/**
	 * фильтруем
	 * @param array $filter
	 * @return Collection|static[]
	 */
	public static function searchResult(array $filter)
    {
		$scoresRaw = DB::table('schedule as s')
			->select(
			    'score.*',
                'u.role_id',
                's.teacher_id',
                'st.weight',
                DB::raw('l.id as lesson_id'),
                DB::raw('l.name as lesson_name')
            )
            ->leftJoin('lesson as l', 's.lesson_id', '=', 'l.id')
            ->leftjoin('score', 'score.schedule_id', '=', 's.id')
			->leftJoin('users as u', 'score.student_id', '=', 'u.id')
            ->leftjoin('score_types as st', 'score.type_id', '=', 'st.id')
           // ->leftJoin('student_group_students', 'student_group_students.group_id', '=', 's.group_id' )
            ->where(static function ($query) {
			   /** @var Builder $query */
			   $query
                    ->where('u.role_id', 2)
                    ->orWhereNull('u.role_id');
            })
            ->where('s.year', $filter['year']['value'] ?? Year::getInstance()->getYear())
            ->when(!empty($filter['period']['value']), static function ($query) use ($filter) {
                $query->whereBetween('date', Period::defineFirstAndLastDays($filter));
            })

//			->when(isset($filter['date']) && $filter['date']['value'], function ($query) use ($filter) {
//				/** @var \Illuminate\Database\Eloquent\Builder $query */
//				$query->where(function($query) use ($filter) {
//					/** @var \Illuminate\Database\Eloquent\Builder $query */
//					$query
//                        ->whereBetween('score.date',
//                            array_map(
//                                function($e) {
//                                    return Carbon::parse($e)->toDateTimeLocalString();
//                                },
//                                $filter['date']['value']
//                            )
//                        )
//                        ->orWhereNull('score.date');
//                });
//			})
			->when(isset($filter['grade_id']['value']), static function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('s.grade_id', $filter['grade_id']['value']);
			})
            ->when(isset($filter['grade_letter']['value']), static function ($query) use ($filter) {
                /** @var Builder $query */
                $query->where('u.class_letter', $filter['grade_letter']['value']);
            })
			->when($filter['student_id']['value'], static function ($query) use ($filter) {
			    $student = User::find($filter['student_id']['value']);
				/** @var Builder $query */
                $query
                    ->where(static function($query) use ($student) {
						/** @var Builder $query */
						$query
                            ->where('score.student_id', $student->id)
                            ->orWhereNull('score.student_id');
                    });

//                    ->where(function($query) use ($student) {
//                        $query
//                            ->where('s.all_class', 1)
//                            ->orWhere('s.grade_letter', $student->class_letter)
//                            ->orWhere('student_group_students.student_id', $student->id);
//                    });
			})
            ->orderBy('l.name', 'asc')
            ->orderBy('score.date', 'asc')
			->get();

		$scores = [];

		foreach ($scoresRaw as $score) {
		    if (empty($scores[$score->lesson_id])) {
                $scores[$score->lesson_id] = [
                    'lesson_id' => $score->lesson_id,
                    'lesson_name' => $score->lesson_name,
                    'scores' => []
                ];
            }
            $scores[$score->lesson_id]['scores'][] = [
                'value' => $score->value,
                'weight' => $score->weight,
                'date' => $score->date
            ];
        }

		return $scores;
	}

	/**
	 * Вычисление среднего балла по школе, классу, ученику, предмету, учителю
	 * @param array $filter
	 * @return null|array
	 */
	public static function weightedAverageScore(array $filter, $groupBy = 'lesson_id'): ?array {
	    $scoreQuery =  Score::query()
			->select('score.lesson_id', DB::raw('sum(if(score.value=\'.\',2,score.value)*t.weight)/sum(t.weight) as avg'))
            ->leftJoin('score_types as t', 'score.type_id', '=', 't.id')
			->join('users as u', 'score.student_id', '=', 'u.id')
			->where('u.role_id', 2)
			->when(!empty($filter['date']['value']) && empty($filter['period']['value']), function ($query) use ($filter) {
				/** @var Builder $query */
				$query->whereBetween('date',
					array_map(
						function($e) {
							return Carbon::parse($e)->toDateTimeLocalString();
						},
						$filter['date']['value']
					)
				);
			})
			->when(!empty($filter['period']['value']), function ($query) use ($filter) {
				/** @var Builder $query */
				$query->whereBetween('date', Period::defineFirstAndLastDays($filter));
			})
			->when($filter['student_id']['value'], function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('score.student_id', $filter['student_id']['value']);
			})
            ->when(!empty($groupBy), function ($query) use ($groupBy) {
                $query
                    ->groupBy($groupBy);
            });

	    if(!empty($groupBy)) {
            return $scoreQuery->pluck('avg', $groupBy)->toArray();
        }
	    else {
            return $scoreQuery->pluck('avg')->toArray();
        }
	}

	/**
	 * создаем фильтр для score-all
	 * @param Request $request
	 * @return array
	 */
	public static function createFilterScoreAll(Request $request): array {

        $grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));
        $periodType = $grade->isHighSchool ? 'half' : 'quarter';

        return [
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Grade::getActive(),
				'value' => $grade->id,
				'name_field' => 'numberLetter',
                'multiple' => false
			],
            'grade_letter' => [
                'title' => 'Класс',
                'type' => 'select',
                'options' => (object)[
                    (object)['id'=> 'А', 'name'=> 'А'],
                    (object)['id'=> 'Б', 'name'=> 'Б'],
                    (object)['id'=> 'В', 'name'=> 'В']
                ],
                'value' => $request->get('grade_letter', null),
                'name_field' => 'name',
                'multiple' => false
            ],
            'period[]' => [
                'title' => 'Период',
                'type' => 'select',
                'options' => array_map(
                    function ($k, $v) {
                        return (object)['id'=> $k, 'name'=> $v];
                    },
                    array_keys(Period::$periodNames),
                    Period::$periodNames
                ),
                'value' => $request->get('period',[1]),
                'name_field' => 'name',
                'multiple' => true
            ],
			'student_id' => [
				'title' => 'Ученик',
				'type' => 'select',
				'options' => User::where('role_id', 2)->orderBy('name', 'ASC')->get(),
				'value' => $request->get('student_id'),
				'name_field' => 'name',
                'multiple' => false
			]
		];
	}

	/**
	 * фильтруем
	 * @param array $filter
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function searchResultScoreAll(array $filter) {
		return User::query()
			->where('role_id', 2)
			->when($filter['grade_id']['value'], function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('users.class', $filter['grade_id']['value']);
			})
            ->when($filter['grade_letter']['value'], function ($query) use ($filter) {
                /** @var Builder $query */
                $query->where('users.class_letter', $filter['grade_letter']['value']);
            })
			->when($filter['student_id']['value'], function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('id', $filter['student_id']['value']);
			})
			->orderBy('users.name', 'ASC')
			->get();
	}

	/**
	 * Получаем ВСЕ оценки на квартал или полугодья для ВСЕХ учеников одного класса
	 * @param array $filter
	 * @return Collection|static[]
	 */
	public static function searchResultScoreAvg(array $filter)
    {
		return User::query()
			->select(
			    'users.*',
                's.value',
                's.lesson_id',
                't.weight',
                'l.name as less_name'
            )
			->where('role_id', 2)
			->leftJoin('score as s', static function ($join) use ($filter) {
				/** @var JoinClause $join */
				$join->on('users.id', '=',   's.student_id');
				$days = Period::defineFirstAndLastDays($filter);
				$join->on('s.date', '>=', DB::raw("'".$days[0]."'"));
				$join->on('s.date', '<=', DB::raw("'".$days[1]."'"));
			})
            ->leftJoin('score_types as t', 's.type_id', '=', 't.id')
            ->leftJoin('lesson as l', 's.lesson_id', '=', 'l.id')
            ->when($filter['grade_id']['value'], static function ($query) use ($filter) {
				$gradeId = $filter['grade_id']['value'];
				/** @var Builder $query */
				$query->where('users.class', $gradeId);
			})
            ->when(isset($filter['grade_letter']) && $filter['grade_letter']['value'], function ($query) use ($filter) {
                /** @var Builder $query */
                $query->where('users.class_letter', $filter['grade_letter']['value']);
            })
			->orderBy('users.name', 'ASC')
			->get();
	}

	/**
	 * @deprecated кажется не используем
	 * создаем массив с оценками по периодам (среднее взвешенное)
	 * @param \Illuminate\Database\Eloquent\Collection|static[] $classStudents
	 * @return array
	 */
	public static function fillStudentsScoresAll($classStudents): array {
		$periodLetter = 'ch';
		if ($classStudents[0]->grade->number > Grade::NINTH_GRADE) {
			$periodLetter = 'p';
		}

		$studentsScores = [];
		foreach ($classStudents as $st) {
			$studentsScores[$st->id]['name'] = $st->name;
			foreach ($st->scorePeriod as $sc) {
				$period = $periodLetter.'1';
				foreach (Period::$lastDays as $k => $p) {
					if (Carbon::parse($sc->date)->timestamp < $p) {
						$period = $periodLetter.($k+1);
						break;
					}
				}
				$value = Score::POINT === $sc->value ? 0 : $sc->value;
				if (isset($studentsScores[$st->id][$sc->lesson_id][$period]['dividend'])) {
					$studentsScores[$st->id][$sc->lesson_id][$period]['dividend'] += $value; // * $sc->type->weight;
					$studentsScores[$st->id][$sc->lesson_id][$period]['divisor'] += $value > 0 ? 1 : 0; //$sc->type->weight;
				} else {
					$studentsScores[$st->id][$sc->lesson_id][$period]['dividend'] = $value; //* $sc->type->weight;
					$studentsScores[$st->id][$sc->lesson_id][$period]['divisor'] = $value > 0 ? 1 : 0; //$sc->type->weight;
				}
			}
		}
		return $studentsScores;
	}

    public static function fillStudentsScoresAllAvg($classStudents): array {
        $periodLetter = 'ch';
        if ($classStudents[0]->grade->number > Grade::NINTH_GRADE) {
            $periodLetter = 'p';
        }

        $studentsScores = [];
        foreach ($classStudents as $st) {
            $studentsScores[$st->id]['name'] = $st->name;
            foreach ($st->score as $sc) {
                $period = $periodLetter.'1';
                foreach (Period::$lastDays as $k => $p) {
                    if (Carbon::parse($sc->date)->timestamp < $p) {
                        $period = $periodLetter.($k+1);
                        break;
                    }
                }
                $value = Score::POINT === $sc->value ? 2 : $sc->value;
                if (isset($studentsScores[$st->id][$sc->lesson_id][$period]['dividend'])) {
                    $studentsScores[$st->id][$sc->lesson_id][$period]['dividend'] += $value * $sc->type->weight;
                    $studentsScores[$st->id][$sc->lesson_id][$period]['divisor'] += $sc->type->weight;
                } else {
                    $studentsScores[$st->id][$sc->lesson_id][$period]['dividend'] = $value * $sc->type->weight;
                    $studentsScores[$st->id][$sc->lesson_id][$period]['divisor'] = $sc->type->weight;
                }
            }
        }
        return $studentsScores;
    }

	/**
	 * создаем массив с оценками по периодам (проставление учителем)
	 * @param \Illuminate\Database\Eloquent\Collection|static[] $classStudents
	 * @return array
	 */
	public static function fillStudentsScoresAllFromTeacher($classStudents): array {
		$studentsScores = [];
		foreach ($classStudents as $st) {
			$studentsScores[$st->id]['name'] = $st->name;
			$studentsScores[$st->id]['score'][1] = Score::scorePeriodByPeriod($st->id, 1);//@todo продумать логику для вывода всех оценок
            $studentsScores[$st->id]['score'][2] = Score::scorePeriodByPeriod($st->id, 2);
            $studentsScores[$st->id]['score'][3] = Score::scorePeriodByPeriod($st->id, 3);
            $studentsScores[$st->id]['score'][4] = Score::scorePeriodByPeriod($st->id, 4);
            $studentsScores[$st->id]['score'][5] = Score::scorePeriodByPeriod($st->id, 5);
            $studentsScores[$st->id]['score'][6] = Score::scorePeriodByPeriod($st->id, 6);
            $studentsScores[$st->id]['score'][7] = Score::scorePeriodByPeriod($st->id, 7);

        }
		return $studentsScores;
	}

	/**
	 * @param Request $request
	 * @param array $filter
	 * @return array
	 */
	public static function createScoreAllData(Request $request, array $filter): array {
		if ($request->get('grade_id') > 0 || $request->get('student_id') > 0) {
			$classStudents = self::searchResultScoreAll($filter);

			if (!empty($classStudents[0])) {
				//@todo переделать как scoreAvg
				Period::lastDaysPeriod($classStudents[0]->grade->number, true, $filter['year']['value']);
				Period::cutPeriodNames($classStudents[0]->grade->number);//todo убрать для эксель
				$studentsScores = self::fillStudentsScoresAllFromTeacher($classStudents);
			}
		}
		return $studentsScores??[];
	}

    public static function createScoreAllAvgData(Request $request, array $filter): array {
        if ($request->get('grade_id') > 0 || $request->get('student_id') > 0) {
            $classStudents = self::searchResultScoreAll($filter);

            if (!empty($classStudents[0])) {
                //@todo переделать как scoreAvg
                Period::lastDaysPeriod($classStudents[0]->grade->number);
                Period::cutPeriodNames($classStudents[0]->grade->number);//todo убрать для эксель
                $studentsScores = self::fillStudentsScoresAllAvg($classStudents);
            }
        }
        return $studentsScores??[];
    }

	/**
	 * @deprecated кажется не используем
	 * фильтруем (среднее взвещение)
	 * @param array $filter
	 * @return Collection|static[]
	 */
	public static function takeLessonsScoreAvg(array $filter)
    {
		return Schedule::query()
			->select('l.*')
			->join('lesson as l', 'schedule.lesson_id', '=', 'l.id')
			->distinct()
			->when($filter['grade_id']['value'], static function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('grade_id', $filter['grade_id']['value']);
			})
            ->where('year', Year::getInstance()->getYear())
			->orderBy('l.name', 'asc')
			->get();
	}

	/**
	 * фильтруем (проставлениие учителем)
	 * @param array $filter
	 * @param null $student
	 * @return Collection|static[]
	 */
	public static function takeLessonsScoreAll(array $filter,  $student = null) {
		return Schedule::query()
			->select(
				'schedule.*',
				'lesson.name'
			)
			->leftJoin('grade', 'schedule.grade_id', '=', 'grade.id')
			->leftJoin('student_group_students', 'student_group_students.group_id', '=', 'schedule.group_id' )

            ->leftJoin('lesson', 'lesson.id', '=', 'schedule.lesson_id')
			->where('schedule.grade_id', $filter['grade_id']['value'])
			->where('schedule.year', Year::getInstance()->getYear())
			->where(function($query) use ($student) {
				if (isset($student->class_letter, $student->id)) {
					/** @var Builder $query */
					$query
						->where('all_class', 1)
						->orWhere('grade_letter', $student->class_letter)
						->orWhere('student_group_students.student_id', $student->id);
				}
			})
			->groupBy('lesson.id')
			->orderBy('lesson.name', 'asc')
			->get();
	}

	/**
	 * создаем фильтр для score-all
	 * @param Request $request
	 * @return array
	 */
	public static function createFilterAttendanceAll(Request $request): array {
		return [
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Grade::getActive(),
				'value' => $request->get('grade_id'),
				'name_field' => 'numberLetter'
			],
            'grade_letter' => [
                'title' => 'Класс',
                'type' => 'select',
                'options' => [
                    (object)['id'=> 'А', 'name'=> 'А'],
                    (object)['id'=> 'Б', 'name'=> 'Б'],
                    (object)['id'=> 'В', 'name'=> 'В']
                ],
                'value' => $request->get('grade_letter', null),
                'name_field' => 'name',
                'multiple' => false
            ],
			'student_id' => [
				'title' => 'Ученик',
				'type' => 'select',
				'options' => User::where('role_id', 2)->orderBy('name', 'ASC')->get(),
				'value' => $request->get('student_id'),
				'name_field' => 'name'
			],
			'period' => [
				'title' => 'Период',
				'type' => 'select',
				'options' => Period::$periodNames,
				'value' => $request->get('period')
			]
		];
	}

	/**
	 * фильтруем
	 * @param array $filter
	 * @return Collection|static[]
	 */
	public static function searchResultAttendanceAll(array $filter)
{
		return  User::query()
			->select('users.*', 'a.type')
			->where('role_id', 2)
			->leftJoin('attendance as a', static function($join) use ($filter) {
				/** @var JoinClause $join */
				$join->on('users.id', '=', 'a.student_id');
				$days = Period::defineFirstAndLastDays($filter);
				$join->on('a.date', '>=',DB::raw("'".$days[0]."'"));
				$join->on('a.date', '<=',DB::raw("'".$days[1]."'"));
			})
			->when($filter['grade_id']['value'], static function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('users.class', $filter['grade_id']['value']);
			})
			->when($filter['student_id']['value'], static function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('users.id', $filter['student_id']['value']);
			})
			->limit(1000)
			->orderBy('users.name', 'ASC')
			->get();
	}

	/**
	 * @param  \Illuminate\Database\Eloquent\Collection|static[] $classStudents
	 * @return array
	 */
	public static function fillStudentsAttendanceAll($classStudents): array {
		$studentAttendance = [];
		foreach ($classStudents as $s) {
			if (isset($studentAttendance[$s->id])) {
				if ('absent' == $s->type) {
					$studentAttendance[$s->id]['day']['absent']++;
				} else {
					$studentAttendance[$s->id]['day']['late']++;
				}
			} else {
				$studentAttendance[$s->id]['name'] = $s->name;
				$studentAttendance[$s->id]['day']['absent'] = 'absent' == $s->type?1:0;
				$studentAttendance[$s->id]['day']['late'] = 'late' == $s->type?1:0;
			}
		}

		return $studentAttendance;
	}

	/**
	 * создаем массив для КАЖДОГО ученика по ВСЕМ предметам, по которым у него оценки
	 * для расчёта СРЕДНЕГО ВЗВЕШЕННОГО балла по периодам.
	 * Например:
	 * По русскому языку 2 оценки у ученика X:
	 * 3 (вес 1) и 5 (вес 4)
	 * (3 + 20 )/5 = 4.6
	 * в массиве увидим  ['dividend' => 23, 'divisor' => 5]
	 * @param CollectionDb|static[] $classStudents
	 * @return array
	 */
	public static function fillStudentsScoresAvg($classStudents): array {
		$studentsScores = [];
		foreach ($classStudents as $st) {
			if (!isset($studentsScores[$st->id]['name'])) {
				$studentsScores[$st->id]['name'] = $st->name;
			}

			$value = Score::POINT === $st->value ? Score::BAD_SCORE : $st->value;
			if (!empty($st->lesson_id)) {
				if (isset($studentsScores[$st->id]['scores'][$st->lesson_id]['dividend'])) {
					$studentsScores[$st->id]['scores'][$st->lesson_id]['dividend'] += $value * $st->weight;
					$studentsScores[$st->id]['scores'][$st->lesson_id]['divisor'] += $st->weight;
				} else {
					$studentsScores[$st->id]['scores'][$st->lesson_id]['dividend'] = $value * $st->weight;
					$studentsScores[$st->id]['scores'][$st->lesson_id]['divisor'] = $st->weight;
				}
                if (!isset($studentsScores[$st->id]['scores'][$st->lesson_id]['less_name'])) {
                    $studentsScores[$st->id]['scores'][$st->lesson_id]['less_name'] = $st->less_name;
                }
			}
		}
		return $studentsScores;
	}

	/**
	 * @param Request $request
	 * @param array $filter
	 * @return array
	 */
	public static function createScoreAvgData(Request $request, array $filter): array {
		if ($request->get('grade_id') > 0) {
			$classStudents = self::searchResultScoreAvg($filter);
			if (!empty($classStudents[0])) {
				Period::cutPeriodNames($classStudents[0]->grade->number);//@todo убрать для эксель
				$filter['period']['options'] = Period::$periodNames;
				$studentsScores = self::fillStudentsScoresAvg($classStudents);
				$studentsScoresClass = self::fillStudentsScoresAvgClass($studentsScores);
				$studentsScores = self::clearStudentsScoresAvg($studentsScores, $filter);
			}
		}

		return [
			'studentsScoresClass' => $studentsScoresClass ?? [],
			'studentsScores' => $studentsScores ?? []
		];
	}

	/**
	 * @param Request $request
	 * @param array $filter
	 * @return array
	 */
	public static function createAttendanceAllData(Request $request, array $filter): array {
		if ($request->get('grade_id') > 0 || $request->get('student_id') > 0) {
			$classStudents = self::searchResultAttendanceAll($filter);
			if (!empty($classStudents[0])) {
				Period::cutPeriodNames($classStudents[0]->grade->number);//@todo убрать для эксель
				$filter['period']['options'] = Period::$periodNames;
				$studentAttendance = self::fillStudentsAttendanceAll($classStudents);
			}
		}

		return [
			'classStudents' => $classStudents ?? [],
			'studentAttendance' => $studentAttendance ?? []
		];
	}

	/**
	 * @param Request $request
	 * @param array $filter
	 * @return array
	 */
	public static function createScoreSchoolData(Request $request, array $filter): array
    {
        $period = $request->get('period');
		if (null !== $period && '' !== $period) {
			$schoolType = self::SCHOOL_REPORT;
			for ($grade = 5; $grade <= 11; $grade ++) {
				$filter['grade_id']['value'] = Year::getInstance()->gradeIdFromClass($grade);
				$classStudents = self::searchResultScoreAvg($filter);

				if (!empty($classStudents[0])) {
					$studentsScores = self::fillStudentsScoresAvg($classStudents);
					$scoreQuantity = self::fillScoreQuantityClassTeacher($studentsScores);
					$schoolType[$grade] = self::fillGradeType($scoreQuantity);
                    self::addCollectiveGradeType($schoolType, $grade);
				}
			}
		}

		return $schoolType ?? [];
	}

	/**
	 * создаем массив с оценками по периодам для класса
	 * @param array $studentsScores
	 * @return array
	 */
	public static function fillStudentsScoresAvgClass(array &$studentsScores): array {
		$studentsScoresClass = [];
		foreach ($studentsScores as $k => $st) {
			if (isset($st['scores'])) {
				foreach ($st['scores'] as $k2 => $st2) {
					$studentsScores[$k]['scores'][$k2]['total'] = $st2['dividend'] / $st2['divisor'];
					if (isset($studentsScoresClass[$k2]['dividend'])) {
						$studentsScoresClass[$k2]['dividend'] += $studentsScores[$k]['scores'][$k2]['total'];
						$studentsScoresClass[$k2]['divisor']++;
					} else {
						$studentsScoresClass[$k2]['dividend'] = $studentsScores[$k]['scores'][$k2]['total'];
						$studentsScoresClass[$k2]['divisor'] = 1;
					}
				}
			}
		}
		return $studentsScoresClass;
	}

	/**
	 * Создаем массив с оценками: сколько 5, сколько 4 т.д. исходя из СРЕДНЕГО ВЗВЕШЕННОГО балла.
	 * Например:
	 * По русскому языку 2 оценки у ученика X:
	 * 3 (вес 1) и 5 (вес 4)
	 * в массиве $studentsScores увидим  ['dividend' => 23, 'divisor' => 5]
	 * (3 + 20 )/5 = 4.6
	 * То есть, мы считаем что у ученика 5 по русскому языку (после окруления).
	 * @param array $studentsScores
	 * @return array
	 */
	public static function fillScoreQuantityClassTeacher(array $studentsScores): array {
		$scoreQuantity = [];
		foreach ($studentsScores as $k => $st) {
			$scoreQuantity[$k]['name'] = $st['name'];
			$scoreQuantity[$k]['total'][5]['quantity'] = 0;
			$scoreQuantity[$k]['total'][5]['subject'] = [];
			$scoreQuantity[$k]['total'][4]['quantity'] = 0;
            $scoreQuantity[$k]['total'][4]['subject'] = [];
            $scoreQuantity[$k]['total'][3]['quantity'] = 0;
            $scoreQuantity[$k]['total'][3]['subject'] = [];
			$scoreQuantity[$k]['total'][2]['quantity'] = 0;
            $scoreQuantity[$k]['total'][2]['subject'] = [];

			if (isset($st['scores'])) {
				foreach ($st['scores'] as $st2) {
					$score = round($st2['dividend'] / $st2['divisor']);
					switch ($score) {
						case 5:
							$scoreQuantity[$k]['total'][5]['quantity']++;
                            $scoreQuantity[$k]['total'][5]['subject'][] = $st2['less_name'];
                            break;
						case 4:
							$scoreQuantity[$k]['total'][4]['quantity']++;
                            $scoreQuantity[$k]['total'][4]['subject'][] = $st2['less_name'];
                            break;
						case 3:
							$scoreQuantity[$k]['total'][3]['quantity']++;
                            $scoreQuantity[$k]['total'][3]['subject'][] = $st2['less_name'];
                            break;
						default:
							$scoreQuantity[$k]['total'][2]['quantity']++;
                            $scoreQuantity[$k]['total'][2]['subject'][] = $st2['less_name'];
                    }
				}
			}
		}
		return $scoreQuantity;
	}

	/**
	 * Делим учеников по успеваемости
	 * @param array $scoreQuantity
	 * @return array
	 */
	public static function fillStudentTypeClassTeacher(array $scoreQuantity): array {
		$studentType = [];
		if (!empty($scoreQuantity)) {
			foreach($scoreQuantity as $k => $s) {
				if ($s['total'][5] > 0 && $s['total'][4] === 0 && $s['total'][3] === 0 && $s['total'][2] === 0) {
					$studentType['stud']['perfect'][] = $s['name'];
				}

				if ($s['total'][5] > 0 && $s['total'][4] === 1 && $s['total'][3] === 0 && $s['total'][2] === 0) {
					$studentType['stud']['oneGood'][] = $s['name'];
				}

				if ($s['total'][5] > 0 && $s['total'][4] > 1 && $s['total'][3] === 0 && $s['total'][2] === 0) {
					$studentType['stud']['perfectGood'][] = $s['name'];
				}

				if ($s['total'][5] > 0 && $s['total'][4] > 0 && $s['total'][3] === 1 && $s['total'][2] === 0) {
					$studentType['stud']['oneRegular'][] = $s['name'];
				}

				if ($s['total'][5] === 0 && $s['total'][4] > 0 && $s['total'][3] === 0 && $s['total'][2] === 0) {
					$studentType['stud']['perfectGood'][] = $s['name'];
				}

				if ($s['total'][5] > 0 && $s['total'][4] > 0 && $s['total'][3] > 1 && $s['total'][2] === 0) {
					$studentType['stud']['normal'][] = $s['name'];
				}

				if ($s['total'][5] === 0 && $s['total'][4] === 0 && $s['total'][3] > 0 && $s['total'][2] === 0) {
					$studentType['stud']['normal'][] = $s['name'];
				}

				if ($s['total'][2] > 0) {
					$studentType['stud']['bad'][] = $s['name'];
				}

				if ($s['total'][5] === 0 && $s['total'][4] === 0 && $s['total'][3] === 0 && $s['total'][2] === 0) {
					$studentType['stud']['noInfo'][] = $s['name'];
				}
			}

			$studentsNoInfo = isset($studentType['stud']['noInfo'])?count($studentType['stud']['noInfo']):0;
			$studentType['total'] = count($scoreQuantity) - $studentsNoInfo;
			if ($studentType['total'] > 0) {
				$studentsBad = isset($studentType['stud']['bad'])?count($studentType['stud']['bad']):0;
				$studentsOneRegular = isset($studentType['stud']['oneRegular'])?count($studentType['stud']['oneRegular']):0;
				$studentsNormal = isset($studentType['stud']['normal'])?count($studentType['stud']['normal']):0;
				$studentType['absolute']['up'] = $studentType['total'] - $studentsBad;
				$studentType['absolute']['percentage'] = round(($studentType['absolute']['up']*100)/$studentType['total']);
				$studentType['quality']['up'] = $studentType['total'] - $studentsBad -  $studentsOneRegular - $studentsNormal;
				$studentType['quality']['percentage'] = round(($studentType['quality']['up']*100)/$studentType['total']);
			}
		}

		return $studentType;
	}

	/**
	 * @param Request $request
	 * @param array $filter
	 * @return array
	 */
	public static function createClassTeacherData(Request $request, array $filter): array {
		if ($request->get('grade_id') > 0) {
			$classStudents = self::searchResultScoreAvg($filter);
			if (!empty($classStudents[0])) {
				Period::cutPeriodNames($classStudents[0]->grade->number);
				$filter['period']['options'] = Period::$periodNames;
				$studentsScores = self::fillStudentsScoresAvg($classStudents);
				$scoreQuantity = self::fillScoreQuantityClassTeacher($studentsScores);
				$studentType = self::fillStudentTypeClassTeacher($scoreQuantity);
			}
		}

		return $studentType ?? [];
	}

	/**
	 * если в фильтре один ученик, то мы очищаем массив $studentsScores от всех ненужных учеников
	 * @param array $studentsScores
	 * @param array $filter
	 * @return array
	 */
	public static function clearStudentsScoresAvg(array $studentsScores, array $filter): array {
		if ($filter['student_id']['value'] > 0) {
			$studentsScores = isset($studentsScores[$filter['student_id']['value']])
				? [$studentsScores[$filter['student_id']['value']]]
				:[];
		}
		return $studentsScores;
	}

	/**
	 * создаем фильтр для score-school
	 * @param Request $request
	 * @return array
	 */
	public static function createFilterScoreSchool(Request $request): array {
		return [
			'period' => [
				'title' => 'Период',
				'type' => 'select',
				'options' => Period::$periodNames,
				'value' => $request->get('period')
			]
		];
	}

	/**
	 * создаем массив с количеством оценок по классам
	 * @param array $scoreQuantity
	 * @return array
	 */
	public static function fillGradeType(array $scoreQuantity): array
    {
		$classType = [];
		if (!empty($scoreQuantity)) {
			foreach ($scoreQuantity as $k => $s) {
				if (isset($classType['quantity'])) {
					$classType['quantity']++;
				} else {
					$classType['quantity'] = 1;
				}
				if ($s['total'][5]['quantity'] > 0 && $s['total'][4]['quantity'] === 0 && $s['total'][3]['quantity'] === 0 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['perfect']['quantity'])) {
						$classType['type']['perfect']['quantity']++;
					} else {
						$classType['type']['perfect']['quantity'] = 1;
					}
				}

				if ($s['total'][5]['quantity'] > 0 && $s['total'][4]['quantity'] === 1 && $s['total'][3]['quantity'] === 0 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['oneGood']['quantity'])) {
						$classType['type']['oneGood']['quantity']++;
					} else {
						$classType['type']['oneGood']['quantity'] = 1;
					}
                }

				if ($s['total'][5]['quantity'] > 0 && $s['total'][4]['quantity'] > 1 && $s['total'][3]['quantity'] === 0 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['perfectGood']['quantity'])) {
						$classType['type']['perfectGood']['quantity']++;
					} else {
						$classType['type']['perfectGood']['quantity'] = 1;
					}
				}

				if ($s['total'][5]['quantity'] > 0 && $s['total'][4]['quantity'] > 0 && $s['total'][3]['quantity'] === 1 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['oneRegular']['quantity'])) {
						$classType['type']['oneRegular']['quantity']++;
					} else {
						$classType['type']['oneRegular']['quantity'] = 1;
					}
				}

				if ($s['total'][5]['quantity'] === 0 && $s['total'][4]['quantity'] > 0 && $s['total'][3]['quantity'] === 0 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['perfectGood']['quantity'])) {
						$classType['type']['perfectGood']['quantity']++;
					} else {
						$classType['type']['perfectGood']['quantity'] = 1;
					}
				}

				if ($s['total'][5]['quantity'] > 0 && $s['total'][4]['quantity'] > 0 && $s['total'][3]['quantity'] > 1 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['normal']['quantity'])) {
						$classType['type']['normal']['quantity']++;
					} else {
						$classType['type']['normal']['quantity'] = 1;
					}
				}

				if ($s['total'][5]['quantity'] === 0 && $s['total'][4]['quantity'] === 0 && $s['total'][3]['quantity'] > 0 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['normal']['quantity'])) {
						$classType['type']['normal']['quantity']++;
					} else {
						$classType['type']['normal']['quantity'] = 1;
					}
				}

				if ($s['total'][2]['quantity'] === 1) {
					if (isset($classType['type']['badOne']['quantity'])) {
						$classType['type']['badOne']['quantity']++;
					} else {
						$classType['type']['badOne']['quantity'] = 1;
					}
                    $classType['type']['badOne']['student'][$k]['name'] = $s['name'];
                    $classType['type']['badOne']['student'][$k]['subject'] = $s['total'][2]['subject'];
				}

				if ($s['total'][2]['quantity'] === 2) {
					if (isset($classType['type']['badTwo']['quantity'])) {
						$classType['type']['badTwo']['quantity']++;
					} else {
						$classType['type']['badTwo']['quantity'] = 1;
					}
                    $classType['type']['badTwo']['student'][$k]['name'] = $s['name'];
                    $classType['type']['badTwo']['student'][$k]['subject'] = $s['total'][2]['subject'];
				}

				if ($s['total'][2]['quantity'] > 2) {
					if (isset($classType['type']['badMore']['quantity'])) {
						$classType['type']['badMore']['quantity']++;
					} else {
						$classType['type']['badMore']['quantity'] = 1;
					}
                    $classType['type']['badMore']['student'][$k]['name'] = $s['name'];
                    $classType['type']['badMore']['student'][$k]['subject'] = $s['total'][2]['subject'];
				}

				if ($s['total'][5]['quantity'] === 0 && $s['total'][4]['quantity'] === 0 && $s['total'][3]['quantity'] === 0 && $s['total'][2]['quantity'] === 0) {
					if (isset($classType['type']['noInfo']['quantity'])) {
						$classType['type']['noInfo']['quantity']++;
					} else {
						$classType['type']['noInfo']['quantity'] = 1;
					}
				}
			}
		}
		return $classType;
	}

	/**
	 * создаем массив с количеством оценок по коллективным классам (5-8 и 9-11)
	 * @param array $schoolType
	 * @param string $grade
	 * @return void
	 */
	public static function addCollectiveGradeType(array &$schoolType, $grade): void
    {
		$collectiveGrade = $grade < Grade::NINTH_GRADE ? 58 : 911;
		if (isset($schoolType[$collectiveGrade]['quantity'])) {
			$schoolType[$collectiveGrade]['quantity'] += $schoolType[$grade]['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['quantity'] = $schoolType[$grade]['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['perfect'])) {
			$schoolType[$collectiveGrade]['type']['perfect'] += $schoolType[$grade]['type']['perfect']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['perfect'] = $schoolType[$grade]['type']['perfect']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['oneGood'])) {
			$schoolType[$collectiveGrade]['type']['oneGood'] += $schoolType[$grade]['type']['oneGood']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['oneGood'] = $schoolType[$grade]['type']['oneGood']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['perfectGood'])) {
			$schoolType[$collectiveGrade]['type']['perfectGood'] += $schoolType[$grade]['type']['perfectGood']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['perfectGood'] = $schoolType[$grade]['type']['perfectGood']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['oneRegular'])) {
			$schoolType[$collectiveGrade]['type']['oneRegular'] += $schoolType[$grade]['type']['oneRegular']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['oneRegular'] = $schoolType[$grade]['type']['oneRegular']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['normal'])) {
			$schoolType[$collectiveGrade]['type']['normal'] += $schoolType[$grade]['type']['normal']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['normal'] = $schoolType[$grade]['type']['normal']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['badOne'])) {
			$schoolType[$collectiveGrade]['type']['badOne'] += $schoolType[$grade]['type']['badOne']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['badOne'] = $schoolType[$grade]['type']['badOne']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['badTwo'])) {
			$schoolType[$collectiveGrade]['type']['badTwo'] += $schoolType[$grade]['type']['badTwo']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['badTwo'] = $schoolType[$grade]['type']['badTwo']['quantity']??null;
		}

		if (isset($schoolType[$collectiveGrade]['type']['badMore'])) {
			$schoolType[$collectiveGrade]['type']['badMore'] += $schoolType[$grade]['type']['badMore']['quantity']??null;
		} else {
			$schoolType[$collectiveGrade]['type']['badMore'] = $schoolType[$grade]['type']['badMore']['quantity']??null;
		}

		$schoolType['total']['quantity'] += $schoolType[$grade]['quantity']??null;
		$schoolType['total']['type']['perfect'] += $schoolType[$grade]['type']['perfect']['quantity']??null;
		$schoolType['total']['type']['onePerfect'] += $schoolType[$grade]['type']['onePerfect']['quantity']??null;
		$schoolType['total']['type']['perfectGood'] += $schoolType[$grade]['type']['perfectGood']['quantity']??null;
		$schoolType['total']['type']['oneRegular'] += $schoolType[$grade]['type']['oneRegular']['quantity']??null;
		$schoolType['total']['type']['normal'] += $schoolType[$grade]['type']['normal']['quantity']??null;
		$schoolType['total']['type']['badOne'] += $schoolType[$grade]['type']['badOne']['quantity']??null;
		$schoolType['total']['type']['badTwo'] += $schoolType[$grade]['type']['badTwo']['quantity']??null;
		$schoolType['total']['type']['badMore'] += $schoolType[$grade]['type']['badMore']['quantity']??null;
	}

	/**
	 * создаем фильтр для class-teacher
	 * @param Request $request
	 * @return array
	 */
	public static function createFilterClassTeacher(Request $request): array {
		return [
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Grade::getActive(),
				'value' => $request->get('grade_id'),
				'name_field' => 'number'
			],
            'grade_letter' => [
                'title' => 'Класс',
                'type' => 'select',
                'options' => [
                    (object)['id'=> 'А', 'name'=> 'А'],
                    (object)['id'=> 'Б', 'name'=> 'Б'],
                    (object)['id'=> 'В', 'name'=> 'В']
                ],
                'value' => $request->get('grade_letter', null),
                'name_field' => 'name',
                'multiple' => false
            ],
			'period' => [
				'title' => 'Период',
				'type' => 'select',
				'options' => Period::$periodNames,
				'value' => $request->get('period')
			]
		];
	}

	/**
	 * учет посещаемости
	 * @param array $filter
	 * @return mixed
	 */
	public static function attendanceStudent($filter) {
		return Attendance::query()
			->select(
				'lesson_id',
				DB::raw("sum(if(type='late', value, 0)) as late"),
				DB::raw("sum(if(type='absent', value, 0)) as absent")
			)
			->when(!empty($filter['date']['value']) && empty($filter['period']['value']), function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->whereBetween('date',
					array_map(
						function($e) {
							return Carbon::parse($e)->toDateTimeLocalString();
						},
						$filter['date']['value']
					)
				);
			})
            ->when(!empty($filter['period']['value']), function ($query) use ($filter) {
                $query->whereBetween('date', Period::defineFirstAndLastDays($filter));
            })
			->when(!empty($filter['period']['value']), function ($query) use ($filter) {
				/** @var Builder $query */
				$query->whereBetween('date', Period::defineFirstAndLastDays($filter));
			})
			->when($filter['student_id']['value'], function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('student_id', $filter['student_id']['value']);
			})
			->groupBy('lesson_id')
            ->where('date', '>=', Carbon::parse(Year::getInstance()->getYearBegin()))
			->get()
			->keyBy('lesson_id');
	}

}
