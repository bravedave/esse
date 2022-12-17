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

/**
 * this controls system authentication
 */
class user {

  public function isValid(): bool {

    logger::debug(sprintf('<%s> %s', 'user is allowed access', __METHOD__));
    return true;  // authentication is successful, user is allowed access
  }

  public static function setID(int $id): void {

    logger::debug(sprintf('<%s> %s', $id, __METHOD__));
  }

  public static function authenticate(string $user, string $password) : ?self {

    return new self;
  }
}
