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

  public function bootstrap($lib = 'bootstrap.js', $file = ''): void {

    if ('bootstrap.js' == $lib) {
    } elseif ('bootstrap-icons.css' == $lib) {

      $path = __DIR__ . '/../../../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css';
      if (file_exists($path)) {

        // logger::info(sprintf('<found : %s> %s', $path, __METHOD__));
        response::css_headers();
        print file_get_contents($path);
      } else {

        logger::info(sprintf('<%s> %s', $path, __METHOD__));
      }
    } elseif ('fonts' == $lib) {

      if ('bootstrap-icons.woff2' == $file || 'bootstrap-icons.woff' == $file) {

        $path = __DIR__ . '/../../../vendor/twbs/bootstrap-icons/font/fonts/' . $file;
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

      // } else {

      logger::info(sprintf('<%s> %s', $file, __METHOD__));
      // }
    } else {

      logger::info(sprintf('<%s> %s', $lib, __METHOD__));
    }
  }
}
