<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\esse\logger;

use users\dao\users;

class user extends bravedave\esse\user {

  public static function authenticate(string $email, string $password): ?self {

    $dao = new users;
    if ($dto = $dao->getUserByEmail($email)) {

      if (password_verify($password, $dto->password)) {

        logger::info(sprintf('<%s> %s', $email, __METHOD__));
        self::setID($dto->id);
        return new self;
      } else {

        logger::info(sprintf('<%s failed password match> %s', $email, __METHOD__));
      }
    } else {

      return null;
    }

    return null;
  }
}
