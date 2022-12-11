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

  static public function url(string $url = '', bool $protocol = false): string {

    return url::toString($url, $protocol);
  }
}
