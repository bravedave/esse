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

  static function css_headers(int $modifyTime = 0, int $expires = 0): void {

    if (!$expires) $expires = config::$CSS_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header('Content-type: text/css');
  }

  static function csv_headers(string $filename = "download.csv", int $modifyTime = 0, int $expires = 0): void {

    self::_common_headers($modifyTime, $expires);
    header("Content-Description: File Transfer");
    header("Content-disposition: attachment; filename=$filename");
    header("Content-type: text/csv");
  }

  static function gif_headers(int $modifyTime = 0, int $expires = 0): void {

    if (!$expires) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header("Content-type: image/gif");
  }

  static function headers(string $mimetype, int $modifyTime = 0, int $expires = 0): void {

    self::_common_headers($modifyTime, $expires);
    header(sprintf('Content-type: %s', $mimetype));
  }

  static function icon_headers(int $modifyTime = 0, int $expires = 0): void {

    if (!$expires) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header('Content-type: image/x-icon');
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

  static function jpg_headers(int $modifyTime = 0, int $expires = 0): void {

    if (!$expires) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header("Content-type: image/jpeg");
  }

  static function html_headers(string $charset = ''): void {

    if (!$charset) $charset = 'UTF-8';

    self::_common_headers();

    if (config::$CONTENT_SECURITY_ENABLED) header("Content-Security-Policy: frame-ancestors 'self'");
    header(sprintf("Content-type: text/html; charset=%s", $charset));
  }

  static function pdf_headers(string $filename = '', int $modifyTime = 0): void {

    self::_common_headers($modifyTime);
    header('Content-type: application/pdf');
    if (!$filename) $filename = 'pdf-' . date('Y-m-d') . '.pdf';

    header(sprintf('Content-Disposition: inline; filename="%s"', $filename));
  }

  static function png_headers(int $modifyTime = 0, int $expires = 0): void {

    if (!$expires) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header("Content-type: image/png");
  }

  public static function serve($path) {

    $debug = false;
    $debug = true;

    if (file_exists($path)) {

      $serve = [
        'avi' => 'video/x-msvideo',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'map' => 'text/plain',
        'mp4' => 'video/mp4',
        'mov' => 'video/quicktime',
        'txt' => 'text/plain',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      ];

      $path_parts = pathinfo($path);
      $mimetype = mime_content_type($path);

      if ($debug) logger::debug(sprintf('<%s> %s', $mimetype, __METHOD__));

      if ('image/jpeg' == $mimetype) {

        if (strstr($path, url::$URL . 'images/')) {

          self::jpg_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
        } else {

          self::jpg_headers(filemtime($path));
        }
        readfile($path);
        if ($debug) logger::debug("served: $path");
      } elseif (isset($path_parts['extension'])) {
        $ext = strtolower($path_parts['extension']);

        if ($ext == 'css') {

          self::css_headers(filemtime($path));
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'js') {

          $expires = 0;
          if (strstr($path, 'jquery-'))
            $expires = config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'inputosaurus.js'))
            $expires = config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'tinylib.js'))
            $expires = config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'moment.min.js'))
            $expires = config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'bootstrap.min.js'))
            $expires = config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'brayworthlib.js'))
            $expires = config::$JQUERY_EXPIRE_TIME;
          elseif (strings::endswith($path, '.js'))
            $expires = config::$JS_EXPIRE_TIME;

          self::javascript_headers(filemtime($path), $expires);
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'eml') {

          self::headers('application/octet-stream', filemtime($path));
          header(sprintf('Content-Disposition: attachment; filename="%s"', $path_parts['basename']));
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'eot') {

          self::headers('application/vnd.ms-fontobject', filemtime($path), config::$FONT_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'ico') {

          self::icon_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'png') {

          if (strstr($path, url::$URL . 'images/')) {

            self::png_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          } else {

            self::png_headers(filemtime($path));
          }

          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'ttf' || $ext == 'otf') {

          self::headers('application/font-sfnt', filemtime($path), config::$FONT_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'woff' || $ext == 'woff2') {

          self::headers('application/font-woff', filemtime($path), config::$FONT_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'jpg' || $ext == 'jpeg') {

          if (strstr($path, url::$URL . 'images/')) {

            self::jpg_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          } else {

            self::jpg_headers(filemtime($path));
          }
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'gif') {

          if (strstr($path, url::$URL . 'images/')) {

            self::gif_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          } else {

            self::gif_headers(filemtime($path));
          }

          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'svg') {

          /**
           * maybe the expire time is like javascript rather than images
           * - this is conservative
           */
          self::headers('image/svg+xml', filemtime($path), config::$JS_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'json') {

          self::json_headers(filemtime($path));
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'xml') {

          self::xml_headers(filemtime($path));
          readfile($path);
          if ($debug) logger::debug("served: $path");
        } elseif ($ext == 'csv') {

          self::csv_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('served: %s', $path));
        } elseif ($ext == 'pdf') {

          self::pdf_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('served: %s', $path));
        } elseif ($ext == 'tif' || $ext == 'tiff') {

          self::tiff_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('served: %s', $path));
        } elseif ($ext == 'zip') {

          self::zip_headers($path_parts['basename'], filemtime($path));
          header("Content-Length: " . filesize($path));
          // logger::info( sprintf('<nested output buffering : %s> %s', ob_get_level(), __METHOD__));
          ob_flush();
          ob_end_flush();
          readfile($path);
          if ($debug) logger::debug(sprintf('served: %s', $path));
        } elseif ($ext == 'html') {

          self::html_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('served: %s', $path));
        } elseif (isset($serve[$ext])) {

          self::headers($serve[$ext], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('served %s from %s', $serve[$ext], $path));
        } elseif ($debug) {

          logger::debug(sprintf('not serving (file type not served): %s', $path));
        }
      } else {

        logger::info(sprintf('not serving : %s', $path));
      }
    } elseif ($debug) {

      logger::debug(sprintf('not serving (not found): %s', $path));
    }
  }

  static function text_headers(int $modifyTime = 0, int $expires = 0): void {

    self::_common_headers($modifyTime, $expires);
    header("Content-type: text/plain");
  }

  static function tiff_headers(string $filename = null, int $modifyTime = 0): void {

    self::_common_headers($modifyTime);
    header("Content-type: image/tiff");

    if (!$filename) $filename = 'binary-' . date('Y-m-d') . '.tiff';
    header(sprintf('Content-Disposition: inline; filename="%s"', $filename));
  }

  static function woff_headers(int $modifyTime = 0, int $length = 0): void {

    self::_common_headers($modifyTime);
    header('Content-type: font/woff');
    if ($length) header(sprintf('Content-length: %s', $length));
  }

  static function woff2_headers(int $modifyTime = 0, int $length = 0): void {

    self::_common_headers($modifyTime);
    header('Content-type: font/woff2');
    if ($length) header(sprintf('Content-length: %s', $length));
  }

  static function xml_headers(int $modifyTime = 0): void {

    self::_common_headers($modifyTime);
    header('Content-type: text/xml');
  }

  static function zip_headers(string $filename = '', int $modifyTime = 0): void {

    self::_common_headers($modifyTime);
    header("Content-type: application/zip");
    if (!$filename) $filename = 'binary-' . date('Y-m-d') . '.zip';

    header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
  }
}
