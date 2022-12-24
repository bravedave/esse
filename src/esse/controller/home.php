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

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('nav'))
      ->main()->then(fn () => $this->load('about'))
      ->aside()->then(fn () => $this->load('aside'));
  }

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
