<?php

namespace App\Custom;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Teacher {

	/**
	 * создаем фильтр
	 * @param Request $request
	 * @return array
	 */
	public static function createFilter(Request $request): array {
		return [
			'teacher_name' => [
				'title' => 'Сотрудник',
				'type' => 'input',
				'value' => $request->get('teacher_name'),
				'name_field' => 'name'
			]
		];
	}

	/**
	 * фильтруем
	 * @param array $filter
	 * @return \Illuminate\Support\Collection|static[]
	 */
	public static function searchResult(array $filter) {
		return User::query()
			->where('role_id', 3)
			->when($filter['teacher_name']['value'], function ($query) use ($filter) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('name', 'like', '%' . $filter['teacher_name']['value'] . '%');
			})
			->orderBy('name')
			->get();
	}

	/**
	 * Всех учителей или же себя (если я учитель)
	 * @return \Illuminate\Support\Collection|static[]
	 */
	public static function getAllTeacherOrSelfTeacher() {
		return User::query()
            ->select('users.*')
            ->join('schedule_teachers', 'schedule_teachers.teacher_id', '=', 'users.id')
			->whereIn('role_id', [1,3])
			->when(Auth::user()->role->name == 'teacher', function ($query) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('users.id', Auth::user()->id);
			})
			->orderBy('users.name', 'asc')
            ->groupBy('users.id')
			->get();
	}
}
