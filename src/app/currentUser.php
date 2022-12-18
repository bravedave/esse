<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

abstract class currentUser extends bravedave\esse\currentUser {

  protected static ?users\dao\dto\users $_user = null;
  public static function id(): int {

    if ($u = self::user()) return $u->id;
    return 0;
  }

  public static function isValid(): bool {

    if ($u = self::user()) return $u->id > 0;
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
