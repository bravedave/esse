<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\resource;

use application;
use MatthiasMullie\Minify;

class importJS extends application {

  protected bool $service = true;

  protected static function _bootstrap() {

    $sourcePath = __DIR__ . '/../../../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.js';
    $src = realpath($sourcePath);
    if ( $src) {

      $minifier = new Minify\JS($src);

      $minifiedPath = __DIR__ . '/../js/bootstrap.bundle.min.js';
      $minifier->minify($minifiedPath);
      printf(sprintf("<%s> %s\n", $minifiedPath, __METHOD__));
    } else {

      printf(sprintf("<%s> %s\n", $sourcePath, __METHOD__));
    }
    printf(sprintf("<end> %s\n", __METHOD__));
  }

  public static function bootstrap() {

    $app = new self(self::startDir());
    $app->_bootstrap();
  }
}
