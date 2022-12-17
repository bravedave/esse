<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace users\dao;

use bravedave\esse\dao;

class users extends dao {
  protected $_db_name = 'users';
  protected $template = __NAMESPACE__ . '\dto\users';

  public function getMatrix(): array {

    $sql = 'SELECT
      `id`,
      `name`,
      `email`,
      `mobile`,
      `admin`,
      `active`
    FROM `users`';
    if ($res = $this->Result($sql)) return $this->dtoSet($res);
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
