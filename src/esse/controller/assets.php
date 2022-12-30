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

use bravedave\esse\cssmin;
use bravedave\esse\jslib;
use bravedave\esse\json;
use bravedave\esse\logger;
use bravedave\esse\response;
use bravedave\esse\url;
use config, controller;

/**
 * assets controller
 *
 *  [WARNING] this controller does not require Authentication
 */
class assets extends controller {
  /**
   * @var bool $requireAuthentication
   */
  protected bool $requireAuthentication = false;

  const vendor = __DIR__ . '/../../../vendor';

  public function bootstrap($lib = 'bootstrap.js', $file = ''): void {

    if ('bootstrap.js' == $lib) {

      $path = self::vendor . '/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
      $path = __DIR__ . '/../js/bootstrap.bundle.min.js';
      response::serve($path);
      // } elseif ('bootstrap.bundle.min.js.map' == $lib) {

      //   $path = self::vendor . '/twbs/bootstrap/dist/js/bootstrap.bundle.min.js.map';
      //   response::json_headers(filemtime($path));
      //   print file_get_contents($path);
    } elseif ('bootstrap.css' == $lib) {

      $path = __DIR__ . '/../css/bootstrap.min.css';
      if ('blue' == config::$THEME) {
        $path = __DIR__ . '/../css/bootstrap-blue.min.css';
      } elseif ('orange' == config::$THEME) {
        $path = __DIR__ . '/../css/bootstrap-orange.min.css';
      } elseif ('pink' == config::$THEME) {
        $path = __DIR__ . '/../css/bootstrap-pink.min.css';
      }

      response::serve($path);
    } elseif ('bootstrap-icons.css' == $lib) {

      $path = __DIR__ . '/../css/bootstrap-icons/bootstrap-icons.css';
      if (file_exists($path)) {

        response::serve($path);
      } else {

        logger::info(sprintf('<not found : %s> %s', $path, __METHOD__));
      }
    } elseif ('fonts' == $lib) {

      if ('bootstrap-icons.woff2' == $file || 'bootstrap-icons.woff' == $file) {

        $path = __DIR__ . '/../css/bootstrap-icons/fonts/' . $file;
        if (file_exists($path)) {

          response::serve($path);
        } else {

          logger::info(sprintf('<not found : %s> %s', $path, __METHOD__));
        }
      }
    } else {

      logger::info(sprintf('<%s> %s', $lib, __METHOD__));
    }
  }

  public function css($lib = 'esse.css'): void {

    if ('esse.css' == $lib) {

      cssmin::viewcss([
        'debug' => false,
        'libName' => 'esseCSS',
        'cssFiles' => config::cssfiles,
        'libFile' => config::tempdir()  . '_home_default.css'
      ]);
    } else {

      logger::info(sprintf('<%s> %s', $lib, __METHOD__));
    }
  }

  public function jquery() {

    response::serve(__DIR__ . '/../js/jquery-3.6.1.min.js');
  }

  public function js($lib = 'esse.js'): void {

    if ('esse.js' == $lib) {

      jslib::viewjs([
        'debug' => false,
        'libName' => 'esseJS',
        'jsFiles' => config::jsfiles,
        'libFile' => config::tempdir() . '_esse_.js'
      ]);
    } elseif ('dopo.js' == $lib) {

      response::javascript_headers(time(), time() - 3600);
      printf('(_ => {
        _.url.root = %s;
      })(_esse_)',
      json_encode(url::$ROOT, JSON_UNESCAPED_SLASHES)
    );
    } else {

      logger::info(sprintf('<%s> %s', $lib, __METHOD__));
    }
  }
}
