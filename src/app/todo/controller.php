<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace todo;

use bravedave\esse;
use bravedave\esse\request;

class controller extends esse\controller {

  protected function _index() : void {

    $this->title = __NAMESPACE__;

    (esse\page::bootstrap())
      ->head($this->title)
      ->body()
      ->then(fn () => $this->load('nav'))
      ->main()
      ->then(fn () => $this->load('blank'))
      ->aside()
      ->then(fn () => $this->load('aside'));
  }

  protected function before() : void {

    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
    config::todo_checkdatabase();
  }

  protected function postHandler() {

    $action = request::post('action');
    parent::postHandler();
  }
}