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
use bravedave\esse\json;
use bravedave\esse\request;

class controller extends esse\controller {

  protected function _index(): void {

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

  protected function before(): void {

    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
    config::todo_checkdatabase();
  }

  protected function postHandler() {

    $action = request::post('action');
    if ('todo-save' == $action) {

      $a = [
        'description' => (string)request::post('description'),
        'complete' => (int)request::post('complete')
      ];

      $dao = new dao\todo;
      if ($id = (int)request::post('id')) {

        $dao->UpdateByID($a, $id);
      } else {

        $dao->Insert($a);
      }

      json::ack($action); // json return { "response": "ack", "description" : "risorsa-save" }
    } else {

      parent::postHandler();
    }
  }

  public function edit($id = 0) {

    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\todo
    ];

    if ($id = (int)$id) {

      $this->data->dto = (new dao\todo)
        ->getByID($id);
      $this->data->title .= ' edit';
    }

    $this->load('edit');
  }
}
