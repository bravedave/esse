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

abstract class response {

  protected static function _common_headers(int $modifyTime = 0, int $expires = 0): void {

    if ($modifyTime) {

      header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $modifyTime)));
      if ($expires) {

        header(sprintf('Expires: %s GMT', gmdate('D, j M Y H:i:s', time() + $expires)));
        header(sprintf('Cache-Control: max-age=%s', $expires));
      } else {

        header(sprintf('Expires: %s GMT', gmdate('D, j M Y H:i:s')));      // Date in the past
        header('Cache-Control: no-cache');
        // header('Pragma: no-cache');                          			// HTTP/1.0
      }
    } else {

      header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s')));  // always modified
      header(sprintf('Expires: %s GMT', gmdate('D, j M Y H:i:s')));      // Date in the past
      header('Cache-Control: no-cache');
      // header('Pragma: no-cache');                          			// HTTP/1.0
    }
  }

  static function javascript_headers(int $modifyTime = 0, int $expires = 0): void {

    self::_common_headers($modifyTime, $expires);
    header('X-Content-Type-Options: nosniff');
    header('Content-type: text/javascript');
  }

  static function json_headers(int $modifyTime = 0, int $length = 0): void {

    self::_common_headers($modifyTime);
    header('Content-type: application/json; charset=utf-8');
    if ($length) header(sprintf('Content-length: %s', $length));
  }

  static function html_headers(string $charset = ''): void {

    if (!$charset) $charset = 'UTF-8';

    self::_common_headers();

    if (config::$CONTENT_SECURITY_ENABLED) header("Content-Security-Policy: frame-ancestors 'self'");
    header(sprintf("Content-type: text/html; charset=%s", $charset));
  }
}
