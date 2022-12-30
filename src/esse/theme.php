<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse;

class theme {

	static function navbar(array $params = []): string {

		$options = array_merge([
			'color' => 'navbar-light bg-light',
			'defaults' => 'navbar navbar-expand-md d-print-none',
			'sticky' => 'sticky-top',
		], $params);

		return implode(' ', $options);
	}

	static function modalHeader(): string {

		return 'text-white bg-primary';
	}
}
