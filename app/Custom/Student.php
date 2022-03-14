<?php

namespace App\Custom;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Grade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection as CollectionEloquent;

class Student
{

	/**
	 * создаем фильтр
	 * @param Request $request
	 * @return array
	 */
	public static function createFilter(Request $request): array
    {
		$gradeId = Auth::user()->curator ? Auth::user()->class : $request->get('grade_id');

		return [
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Auth::user()->curator ? Grade::where('id', Auth::user()->class)->get() : Grade::getActive(),
				'value' => $gradeId,
				'name_field' => 'numberLetter'
			],
            'name' => [
                'title' => 'Имя',
                'type' => 'input',
                'value' => $request->get('name'),
                'name_field' => 'name'
            ]
		];
	}

	/**
	 * @param int $gradeId
	 * @return array|CollectionEloquent|static[]
	 */
	public static function getStudentsByGradeId($gradeId)
    {
		return User::where('role_id', 2)
			->orderBy('name', 'ASC')
			->when($gradeId, static function ($query) use ($gradeId) {
				/** @var Builder $query */
				$query->where('class', $gradeId);
			})
			->get();
	}

	/**
	 * фильтруем
	 * @param array $filter
	 * @return Collection|static[]
	 */
	public static function searchResult(array $filter)
    {
		return User::query()
			->where('role_id', 2)
			->when($filter['grade_id']['value'], function ($query) use ($filter) {
				/** @var Builder $query */
				$query->where('class', $filter['grade_id']['value']);
			})
            ->when($filter['name']['value'], function ($query) use ($filter) {
                /** @var Builder $query */
                $query->where('name', 'like', '%' . $filter['name']['value'] . '%');
            })
			->orderBy('name')
			->get();
	}
}
