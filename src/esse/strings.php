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

abstract class strings {

  static public function rand(string $prefix = 'uid_'): string {

    return $prefix . bin2hex(random_bytes(11));
  }

  static public function url(string $url = '', bool $protocol = false): string {

    return url::toString($url, $protocol);
  }
}
