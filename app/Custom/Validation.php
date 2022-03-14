<?php

namespace App\Custom;

use Illuminate\Http\Request;

class Validation {
	/**
	 * @param Request $request
	 * @param null $id
	 */
	public static function emailValidation(Request $request, $id = null) {
		if ($id) {
			$rules = [
				'email' => 'unique:users,email,'.$id
			];
		} else {
			$rules = [
				'email' => 'unique:users|max:191'
			];
		}

		$customMessages = [
			'unique' => 'Пользователь с такой почтой уже существует.'
		];

		$request->validate($rules, $customMessages);
	}
}
