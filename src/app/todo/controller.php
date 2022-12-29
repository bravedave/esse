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

use bravedave\esse\json;
use bravedave\esse\logger;
use bravedave\esse\page;
use bravedave\esse\request;

class controller extends \controller {

  protected function _index(): void {

    $this->title = config::label;

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('nav'))
      ->main()->then(fn () => $this->load('matrix'))
      ->aside()->then(fn () => $this->load('aside'));
  }

  protected function before(): void {

    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
    config::todo_checkdatabase();
    parent::before();
  }

  protected function postHandler(): void {

    $action = request::post('action');
    if ('get-by-id' == $action) {
      /*
        (_ => {
          $.post(_.url('todo'), {
            action: 'get-by-id',
            id : 1
          }).then(d => {
            if ('ack' == d.response) {
              console.log(d.data);
            } else {
              _.growl(d);
            }
          });

        })(_esse_);
       */
      if ($id = (int)request::post('id')) {

        if ($dto = (new dao\todo)->getByID($id)) {

          json::ack($action)
            ->add('data', $dto);
        } else {

          json::nak($action);
        }
      } else {

        json::nak($action);
      }
    } elseif ('get-matrix' == $action) {
      /*
        (_ => {
          $.post(_.url('todo'),{action: 'get-matrix'})
            .then(d => {
              if ('ack' == d.response) {
                console.table(d.data);
              } else {
                _.growl(d);
              }
            });
        })(_esse_);
       */
      json::ack($action)
        ->add('data', (new dao\todo)->getMatrix());
    } elseif ('todo-save' == $action) {

      $a = [
        'description' => (string)request::post('description'),
        'complete' => (int)request::post('complete')
      ];

      if ($id = (int)request::post('id')) {

        (new dao\todo)->UpdateByID($a, $id);
      } else {

        (new dao\todo)->Insert($a);
      }

      json::ack($action);
    } elseif ('todo-set-complete' == $action || 'todo-set-complete-undo' == $action) {

      if ($id = (int)request::post('id')) {

        (new dao\todo)
          ->UpdateByID([
            'complete' => 'todo-set-complete-undo' == $action ? 0 : 1
          ], $id);
        json::ack($action);
      } else {

        json::nak($action);
      }
    } else {

      parent::postHandler();
    }
  }

  public function edit($id = 0) : void {

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
