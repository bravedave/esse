<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace todo\dao;

use bravedave\esse\dao;

class todo extends dao {
  protected $_db_name = 'todo';
  protected $template = __NAMESPACE__ . '\dto\todo';

  public function Insert($a): int {

    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {

    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
