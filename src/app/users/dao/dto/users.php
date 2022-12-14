<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace users\dao\dto;

use bravedave\esse\dto;

class users extends dto {
	public int $id = 0;

	public string $name = '';

	public bool $admin = false;

	public bool $active = true;

	public string $email = '';

	public string $mobile = '';

	public string $group = '';

	public string $birthdate = '';

	public string $start_date = '';

	function __construct($row = null) {

		$this->start_date = date('Y-m-d');
		parent::__construct($row);
	}
}
