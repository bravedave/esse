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

use config;

abstract class url {
  static public string $URL;
  static public string $HOME;
  static public string $PROTOCOL;

  static function tostring(string $url = '', bool $protocol = false): string {

    if ($protocol) {

      return implode([self::$PROTOCOL, self::$URL, $url]);
    } else {

      return implode([self::$URL, $url]);
    }
  }

  static function write(string $url = '', bool $protocol = false) {

    print self::tostring($url, $protocol);
  }

  static function swrite(string $url = '', bool $protocol = false): string {

    return self::tostring($url, $protocol);
  }

  static function initialize() {
    if (isset(self::$URL)) return;

    $url = '';
    $software = $_SERVER['SERVER_SOFTWARE'] ?? '';

    if (preg_match('@^PHP@', $software)) {

      if (config::use_full_url) {

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80) {

          $url = sprintf('//localhost:%s/', $_SERVER['SERVER_PORT']);
        } else {

          $url = '//localhost/';
        }
      } else {

        $url = '/';
      }
    }

    if (!$url) {

      $server = strtolower($_SERVER['SERVER_NAME'] ?? '');
      $server = preg_replace('@\/$@', '', $server);

      $script = '';
      if (isset($_SERVER['SCRIPT_NAME'])) $script = dirname($_SERVER['SCRIPT_NAME']);
      $script = preg_replace('@(\/|\\\)$@', '', $script);

      $port = '';
      if (preg_match('@^Apache@', $_SERVER['SERVER_SOFTWARE'] ?? '')) {

        if (config::use_full_url) {

          if (!in_array($_SERVER['SERVER_PORT'] ?? 80, [80, 443])) {

            $port = ':' . $_SERVER['SERVER_PORT'];
          }
        }
      }

      $url = sprintf('//%s%s%s/', $server, $port, $script);

      if (config::use_full_url) {

        $script = preg_replace('@/application$@', '', $script);
        $url = sprintf('//%s%s%s/', $server, $port, $script);
      }
    }

    self::$URL = $url;
    self::$HOME = $url;

    $protocol = (
      ($_SERVER['HTTPS'] ?? 'off') !== 'off'
      ||
      (($_SERVER['SERVER_PORT'] ?? 0) == 443)
    ) ? "https:" : "http:";

    self::$PROTOCOL = $protocol;
  }
}

url::initialize();
