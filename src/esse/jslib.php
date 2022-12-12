<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Creates a lib combined file for a js library
 * 	- requires a directory to write to -see tinymce for example:
 * 		=> requires appdir/app/public/js/tinymce to be writable
 *
 * then you can call one file in place of several, if the library is updated,
 * it will recompile it
 *
 * in theory - only used it once ...
*/

namespace bravedave\esse;

use FilesystemIterator;
use GlobIterator, MatthiasMullie;

abstract class jslib {
  public static $debug = false;
  public static $tinylib = false;
  public static $brayworthlib = false;

  protected static $rootPath = null;

  protected static function __createlib($libdir, $jslib, $files, $minify = false) {
    $debug = self::$debug;
    //~ $debug = TRUE;

    if (is_null(self::$rootPath)) {
      self::$rootPath = application::getRootPath() . '/app/public/js';
    }

    if ($libdir) {
      $outputDIR = sprintf('%s/%s', self::$rootPath, $libdir);
    } else {
      $outputDIR = self::$rootPath;
    }

    $output = sprintf('%s/%s', $outputDIR, $jslib);

    if (file_exists(application::getRootPath() . '/app/public/')) {
      if (!(file_exists($outputDIR)) && is_writable(self::$rootPath)) {
        mkdir($outputDIR, 0777, true);
      }

      if (is_writable($outputDIR)) {
        $contents = [];
        foreach ($files as $file) {
          if (realpath($file)) {
            $contents[] = file_get_contents($file);
          } else {
            logger::info('cannot locate library file ' . $file);
            //~ logger::info( realpath( $file));

          }
        }

        $content = implode("\n", $contents);
        if ($minify) {
          $minifier = new \MatthiasMullie\Minify\JS();
          $minifier->add(implode("\n", $contents));
          $content = $minifier->minify();
        }

        file_put_contents($output, $content);
        return (true);
        //~ logger::info( 'no of files = ' . count( $contents));

      } else {
        logger::info(sprintf('%s is not writable - cannot create a library here', $outputDIR));
        logger::info(sprintf('please create a writable data folder : %s', $outputDIR));
        logger::info(sprintf('mkdir --mode=0777 %s', $outputDIR));
      }
    } else {
      logger::info('[root]/app/public/ does not exist');
    }

    return (false);
  }

  protected static function _js_create($options) {
    $input = [];

    if (is_array($options->jsFiles)) {
      foreach ($options->jsFiles as $key => $item) {
        if ($options->leadKey && $key == $options->leadKey) {
          if ($options->debug) logger::info(sprintf('%s :: prepending leadKey %s', $options->libName, $options->leadKey));
          array_unshift($input, file_get_contents($item));
        } else {
          if ($options->debug) logger::info(sprintf('%s :: appending key %s', $options->libName, $key));
          if ($path = realpath($item)) {
            $input[] = file_get_contents($path);
          } else {
            logger::info(sprintf('<cannot find %s> %s', $item, __METHOD__));
          }
        }
      }
    } else {

      $gi = new GlobIterator($options->jsFiles, FilesystemIterator::KEY_AS_FILENAME);
      foreach ($gi as $key => $item) {

        if ($options->leadKey && $key == $options->leadKey) {

          if ($options->debug) logger::info(sprintf('%s :: prepending leadKey %s', $options->libName, $options->leadKey));
          array_unshift($input, file_get_contents($item->getRealPath()));
        } else {

          if ($options->debug) logger::info(sprintf('%s :: appending key %s', $options->libName, $key));
          $input[] = file_get_contents($item->getRealPath());
        }
      }
    }

    if (count($input)) {
      if ($options->minify) {
        $minifier = new MatthiasMullie\Minify\JS;
        $minifier->add($input);

        file_put_contents($options->libFile, $minifier->minify());
      } else {
        file_put_contents($options->libFile, implode($input));
      }
    } else {
      file_put_contents($options->libFile, '');
    }
  }

  protected static function _js_serve($options) {

    $expires = config::$JS_EXPIRE_TIME;
    $modTime = filemtime($options->libFile);
    $age = time() - $modTime;
    if ($age < 3600)
      $expires = 36;

    if ($options->debug) logger::info(sprintf('%s :: serving(%s) %s', $options->libName, $expires, $options->libFile));

    response::javascript_headers(filemtime($options->libFile), $expires);
    print file_get_contents($options->libFile);
  }

  public static function viewjs($params) {

    $options = (object)array_merge([
      'debug' => false,
      'libName' => '',
      'leadKey' => false,
      'jsFiles' => false,
      'libFile' => false,
      'minify' => true
    ], $params);

    if ($options->libFile) {
      if ($options->jsFiles) {
        if (file_exists($options->libFile)) {
          /* test to see if requires update */
          $modtime = 0;

          if (is_array($options->jsFiles)) {
            foreach ($options->jsFiles as $item) {
              $modtime = max([$modtime, filemtime($item)]);
            }
          } else {
            $gi = new GlobIterator($options->jsFiles, FilesystemIterator::KEY_AS_FILENAME);
            foreach ($gi as $key => $item) {
              $modtime = max([$modtime, filemtime($item->getRealPath())]);
            }
          }

          $libmodtime = filemtime($options->libFile);
          if ($libmodtime < $modtime) {
            if ($options->debug) logger::info(sprintf('<%s :: updating %s, latest mod time = %s> %s', $options->libName, $options->libFile, date('r', $modtime), __METHOD__));

            self::_js_create($options);
            self::_js_serve($options);
          } else {
            if ($options->debug) logger::info(sprintf('<%s :: latest version (%s)> %s', $options->libName, $options->libFile, __METHOD__));
            self::_js_serve($options);
          }
        } else {
          /* create and serve */
          if ($options->debug) logger::info(sprintf('<%s :: creating %s> %s', $options->libName, $options->libFile, __METHOD__));

          self::_js_create($options);
          self::_js_serve($options);
        }
      } else {
        throw new Exceptions\LibraryFilesNotSpecified;
      }
    } else {

      throw new Exceptions\FileNotSpecified;
    }
  }
}
