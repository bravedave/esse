<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace home;

use bravedave\esse\page;
use config;

class controller extends \controller {

  protected function _index(): void {

    $this->title = config::$WEBNAME;

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('nav'))
      ->main()->then(fn () => $this->load('about'))
      ->aside()->then(fn () => $this->load('aside'));
  }

  protected function before(): void {

    $this->viewPath[] = __DIR__ . '/views';
    parent::before();
  }

  public function goodbye(): void {

    $this->title = 'Goodbye !';

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => printf('<h1 class="text-center mt-4">%s</h1>', $this->title));
  }
}
