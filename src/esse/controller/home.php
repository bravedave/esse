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

use bravedave\esse\logger;
use bravedave\esse\page;
use bravedave\esse\response;
use config, controller;
use finfo;
use Parsedown;
use SplFileInfo;

/**
 * a default home controller
 */
class home extends controller {

  /**
   * called by index if this method is GET
   */
  protected function _index(): void {

    $this->title = config::$WEBNAME;

    $readme = realpath(__DIR__ . '/../../../readme.md');
    $fc = file_get_contents($readme);

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('nav'))
      ->main()->then(fn () => printf('<div class="markdown-body">%s</div>', Parsedown::instance()->text($fc)))
      ->aside()->then(fn () => $this->load('aside'));
  }
  // ->main()->then(fn () => $this->load('about'))

  function images($file = ''): void {

    if ($file) {

      $exts = ['jpg', 'png', 'svg'];
      $fi = new SplFileInfo($file);
      if (in_array($fi->getExtension(), $exts)) {

        $fileName = $fi->getBasename('.' . $fi->getExtension());
        if ($path = $this->getView($fileName, $exts)) {

          response::serve($path);
        } else {

          logger::info(sprintf('<%s> %s', $fileName, __METHOD__));
          logger::info(sprintf('<not found : %s> %s', $file, __METHOD__));
        }
      }
    }
  }
}
