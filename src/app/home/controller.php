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

use bravedave\esse;
use config;

class controller extends esse\controller {

  protected function _index(): void {

    $this->title = config::$WEBNAME;

    (esse\page::bootstrap())
      ->head($this->title)
      ->body()
      ->then(fn () => $this->load('nav'))
      ->main()
      ->then(fn () => $this->load('main'))
      ->aside()
      ->then(fn () => $this->load('aside'));
  }

  protected function before(): void {

    parent::before();
    $this->viewPath[] = __DIR__ . '/views';
  }

  public function goodbye() {

    $this->title = 'Goodbye !';

    (esse\page::bootstrap())
      ->head($this->title)
      ->body()
      ->then(fn () => printf('<h1 class="text-center mt-4">%s</h1>', $this->title));
  }
}
