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

  public static function id(): int {
    return 0;
  }

  public static function isValid(): bool {
    return true;
  }

  public static function name(): string {
    return '';
  }
}
