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

use RangeException;

abstract class strings {

  static public function endswith(string $string, string $test): bool {

    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen, TRUE) === 0;
  }

  static public function isEmail(string $email): bool {

    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  static public function rand(string $prefix = 'uid_'): string {

    return $prefix . bin2hex(random_bytes(11));
  }

  /**
   * Generate a random string, using a cryptographically secure
   * pseudorandom number generator (random_int)
   *
   * https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
   *
   * @param int $length      How many characters do we want?
   * @param string $keyspace A string of all possible characters
   *                         to select from
   * @return string
   */
  static public function random_string(int $length = 64, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string {

    if ($length < 1) throw new RangeException("Length must be a positive integer");

    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
      $pieces[] = $keyspace[random_int(0, $max)];
    }

    return implode('', $pieces);
  }

  static public function url(string $url = '', bool $protocol = false): string {

    return url::toString($url, $protocol);
  }
}
