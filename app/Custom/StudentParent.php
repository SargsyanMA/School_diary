<?php

namespace App\Custom;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Grade;

class StudentParent {

	/**
	 * создаем фильтр
	 * @param Request $request
	 * @return array
	 */
	public static function createFilter(Request $request): array {

		$grade_id = Auth::user()->curator ? Auth::user()->class : $request->get('grade_id');

		return [
			'grade_id' => [
				'title' => 'Параллель',
				'type' => 'select',
				'options' => Grade::getActive(),
				'value' => $grade_id,
				'name_field' => 'number'
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
	 * @param int $grade_id
	 * @return array|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getParentsByGradeId($grade_id) {
		return User::query()
			->select('users.*', DB::raw("group_concat(students.class, ',') as class"))
			->leftJoin('users as students', 'students.parent_id', 'users.id')
			->where('users.role_id', 4)
			->when($grade_id, function ($query) use ($grade_id) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('students.class', $grade_id);
			})
			->groupBy('users.id')
			->orderBy('name')
			->get();
	}

	/**
	 * фильтруем
	 * @param array $filter
	 * @return \Illuminate\Support\Collection|static[]
	 */
	public static function searchResult(array $filter) {
		return User::query()
			->select('users.*', DB::raw("group_concat(students.class, ',') as class"))
			->leftJoin('users as students', 'students.parent_id', 'users.id')
			->where('users.role_id', 4)
			->when($filter['grade_id']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('students.class', $filter['grade_id']['value']);
			})
            ->when($filter['name']['value'], function ($query) use ($filter) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('name', 'like', '%' . $filter['name']['value'] . '%');
            })
			->groupBy('users.id')
			->orderBy('name')
			->get();
	}
}
