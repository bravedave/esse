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

  /**
   * returns the id of the logged in user, or 0 if no user is logged in
   *
   * @return int
   */
  public static function id(): int {

    if ($u = self::user()) return $u->id;
    return 0;
  }

  /**
   * Controls access to the application,
   * if this function returns true - you are in
   *
   * @return bool
   */
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

  /**
   * returns name of the currentUser
   *
   * @return string
   */
  public static function name(): string {

    if ($u = self::user()) return $u->name;
    return '';
  }

  /**
   * returns the user object of the currentUser
   *
   * @return ?users\dao\dto\users
   */
  public static function user(): ?users\dao\dto\users {

    if (is_null(self::$_user)) {

      self::$_user = user::getCurrentUser();
    }

    return self::$_user;
  }
}
