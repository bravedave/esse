<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace todo\dao\dto;

use bravedave\esse\dto;

class todo extends dto {

  public int $id = 0;

  public string $created = '';
  public string $updated = '';

  public string $description = '';
  public int $complete = 0;
}
