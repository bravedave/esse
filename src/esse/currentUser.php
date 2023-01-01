<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse;

abstract class currentUser {

  /**
   * returns the id of the logged in user, or 0 if no user is logged in
   *
   * @return int
   */
  public static function id(): int {
    return 0;
  }

  /**
   * Controls access to the application,
   * if this function returns true - you are in
   *
   * @return bool
   */
  public static function isValid(): bool {
    return true;
  }

  /**
   * returns name of the currentUser
   *
   * @return string
   */
  public static function name(): string {
    return '';
  }

  public static function option(string $key, null|string $val = null): string {

    return config::option($key, $val);
  }
}
