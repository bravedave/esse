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

use bravedave\esse\page;
use controller;

/**
 * a default home controller
 */
class home extends controller {

  /**
   * called by index if this method is GET
   */
  protected function _index() : void {

    $this->title = 'home - default';

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('nav'))
      ->main()->then(fn () => $this->load('main'))
      ->aside()->then(fn () => $this->load('aside'));
  }
}
