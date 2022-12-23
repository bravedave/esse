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

  public static function getID(): int {

    return (int)session::get('user_id');
    return 0;
  }

  public static function setID(int $id): void {

    session::set('user_id', $id);
    logger::debug(sprintf('<%s> %s', $id, __METHOD__));
  }

  public static function authenticate(string $user, string $password) : ?self {

    return new \user;
  }
}
