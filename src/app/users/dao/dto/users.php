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

	public string $email = '';

	public string $mobile = '';

	public string $password = '';

	public bool $admin = false;

	public bool $active = false;

	public string $created = '';

	public string $updated = '';
}
