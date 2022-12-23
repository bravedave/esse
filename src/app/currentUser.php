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

abstract class currentUser extends bravedave\esse\currentUser {

  protected static int $_userCount = -1;
  protected static ?users\dao\dto\users $_user = null;
  public static function id(): int {

    if ($u = self::user()) return $u->id;
    return 0;
  }

  public static function isValid(): bool {

    if ($u = self::user()) return $u->id > 0;

    if (self::$_userCount < 0) {

      self::$_userCount = (new users\dao\users)->count();
      logger::debug(sprintf('<there are %d users> %s', self::$_userCount, __METHOD__));
    }

    if (!(self::$_userCount)) {

      logger::info(sprintf('<%s> %s', 'allowing access because there are no users', __METHOD__));
      return true;
    }

    return false;
  }

  public static function name(): string {

    if ($u = self::user()) return $u->name;
    return '';
  }

  public static function user(): ?users\dao\dto\users {

    if (is_null(self::$_user)) {

      self::$_user = user::getCurrentUser();
    }

    return self::$_user;
  }
}
