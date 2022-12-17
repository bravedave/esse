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
use strings;

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

  public function getUserByEmail(string $email): ?dto\users {

    if (strings::isEmail($email)) {

      $sql = sprintf(
        'SELECT * FROM `users` WHERE `email` = %s',
        $this->quote($email)
      );

      if ($res = $this->Result($sql)) {

        if ($dto = $res->dto($this->template)) return $dto;
      }
    }

    return null;
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
