<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Creates a min combined file for css
 * - requires a directory to write to
 * 		=> requires appdir/app/public/css/ to be writable
 *
 * then you can call one file in place of several,
 * if any css is updated, it will recompile it
 *
*/

namespace bravedave\esse;

use config, FilesystemIterator;
use GlobIterator, MatthiasMullie;
use RuntimeException;

abstract class cssmin {
  public static $debug = false;

  protected static function _css_create($options) {

    $input = [];
    if (is_array($options->cssFiles)) {

      foreach ($options->cssFiles as $file) {

        if ($options->debug) logger::info(sprintf('<%s :: appending file %s> %s', $options->libName, $file, __METHOD__));
        $input[] = file_get_contents(realpath($file));
      }
    } else {

      $gi = new GlobIterator($options->cssFiles, FilesystemIterator::KEY_AS_FILENAME);

      foreach ($gi as $key => $item) {

        if ($options->leadKey && $key == $options->leadKey) {

          if ($options->debug) logger::info(sprintf('<%s :: prepending leadKey %s> %s', $options->libName, $options->leadKey, __METHOD__));
          array_unshift($input, file_get_contents($item->getRealPath()));
        } else {

          if ($options->debug) logger::info(sprintf('<%s :: appending key %s> %s', $options->libName, $key, __METHOD__));
          $input[] = file_get_contents($item->getRealPath());
        }
      }
    }

    $minifier = new MatthiasMullie\Minify\CSS;
    $minifier->add(implode(PHP_EOL, $input));
    // $content = $minifier->minify();

    file_put_contents($options->libFile, $minifier->minify());
  }

  protected static function _css_serve($options) {

    $expires = config::$CSS_EXPIRE_TIME;
    $modTime = filemtime($options->libFile);
    $age = time() - $modTime;
    if ($age < 3600) $expires = 36;

    if ($options->debug) logger::info(sprintf('<%s :: serving(%s) %s> %s', $options->libName, $expires, $options->libFile, __METHOD__));
    response::css_headers(filemtime($options->libFile), $expires);
    print file_get_contents($options->libFile);
  }

  public static function viewcss($params) {

    $options = (object)array_merge([
      'debug' => false,
      'libName' => '',
      'leadKey' => false,
      'cssFiles' => false,
      'libFile' => false
    ], $params);

    if ($options->libFile) {

      if ($options->cssFiles) {

        if (file_exists($options->libFile)) {
          /* test to see if requires update */
          $modtime = 0;
          if (is_array($options->cssFiles)) {

            foreach ($options->cssFiles as $file) {

              $modtime = max([$modtime, filemtime(realpath($file))]);
            }
          } else {

            $gi = new GlobIterator($options->cssFiles, FilesystemIterator::KEY_AS_FILENAME);
            foreach ($gi as $key => $item) {

              $modtime = max([$modtime, filemtime($item->getRealPath())]);
            }
          }

          $libmodtime = filemtime($options->libFile);
          if ($libmodtime < $modtime) {

            if ($options->debug) logger::info(sprintf('<%s :: updating %s, latest mod time = %s> %s', $options->libName, $options->libFile, date('r', $modtime), __METHOD__));

            self::_css_create($options);
            self::_css_serve($options);
          } else {

            if ($options->debug) logger::info(sprintf('<%s :: latest version (%s)> %s', $options->libName, $options->libFile, __METHOD__));
            self::_css_serve($options);
          }
        } else {

          /* create and serve */
          if ($options->debug) logger::info(sprintf('<%s :: creating %s> %s', $options->libName, $options->libFile, __METHOD__));

          self::_css_create($options);
          self::_css_serve($options);
        }
      } else {

        throw new RuntimeException('The files required to create the Library were Not Specified');
      }
    } else {

      throw new RuntimeException('the file or path was not specified when calling the function');
    }
  }
}
