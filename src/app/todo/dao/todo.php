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
  protected ?string $_db_name1 = 'todo';
  protected ?string $template = __NAMESPACE__ . '\dto\todo';

  public function getMatrix() : array {

    $sql = 'SELECT * FROM `todo`';
    if ( $res = $this->Result($sql)) return $this->dtoSet($res);
    return [];
  }

  public function Insert($a): int {

    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {

    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
