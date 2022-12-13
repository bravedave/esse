<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\controller;

use bravedave\esse;
use bravedave\esse\jslib;
use bravedave\esse\logger;
use bravedave\esse\response;
use config;

/**
 * a default home controller
 */
class assets extends esse\controller {

  const vendor = __DIR__ . '/../../../vendor';

  public function bootstrap($lib = 'bootstrap.js', $file = ''): void {

    if ('bootstrap.js' == $lib) {

      $path = self::vendor . '/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
      response::javascript_headers(filemtime($path), config::$JS_EXPIRE_TIME);
      print file_get_contents($path);
    } elseif ('bootstrap.bundle.min.js.map' == $lib) {

      $path = self::vendor . '/twbs/bootstrap/dist/js/bootstrap.bundle.min.js.map';
      response::json_headers(filemtime($path));
      print file_get_contents($path);
    } elseif ('bootstrap.css' == $lib) {

      $path = __DIR__ . '/../resource/bootstrap-orange.min.css';
      response::css_headers(filemtime($path), config::$CSS_EXPIRE_TIME);
      print file_get_contents($path);
    } elseif ('bootstrap-icons.css' == $lib) {

      $path = self::vendor . '/twbs/bootstrap-icons/font/bootstrap-icons.css';
      if (file_exists($path)) {

        // logger::info(sprintf('<found : %s> %s', $path, __METHOD__));
        response::css_headers(filemtime($path), config::$CSS_EXPIRE_TIME);
        print file_get_contents($path);
      } else {

        logger::info(sprintf('<%s> %s', $path, __METHOD__));
      }
    } elseif ('fonts' == $lib) {

      if ('bootstrap-icons.woff2' == $file || 'bootstrap-icons.woff' == $file) {

        $path = self::vendor . '/twbs/bootstrap-icons/font/fonts/' . $file;
        if (file_exists($path)) {

          // logger::info(sprintf('<found : %s> %s', $path, __METHOD__));
          if ('bootstrap-icons.woff2' == $file) {

            response::woff2_headers();
          } else {

            response::woff_headers();
          }

          print file_get_contents($path);
        } else {

          logger::info(sprintf('<%s> %s', $path, __METHOD__));
        }
      }
    } else {

      logger::info(sprintf('<%s> %s', $lib, __METHOD__));
    }
  }

  public function jquery() {

    $path = __DIR__ . '/../js/jquery-3.6.1.min.js';
    response::javascript_headers(filemtime($path), config::$JS_EXPIRE_TIME);
    print file_get_contents($path);
  }

  public function js($lib = 'esse.js'): void {

    if ('esse.js' == $lib) {

      jslib::viewjs([
        'debug' => false,
        'libName' => 'esse',
        'jsFiles' => config::libfiles,
        'libFile' => config::tempdir() . '_esse_.js'
      ]);
    } else {

      logger::info(sprintf('<%s> %s', $lib, __METHOD__));
    }
  }

}
