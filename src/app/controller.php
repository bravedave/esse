<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class controller extends bravedave\esse\controller {

  protected function before(): void {

    $this->viewPath[] = __DIR__ . '/views/';  // location for application specific views
    parent::before();
  }
}
