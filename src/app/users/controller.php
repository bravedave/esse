<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace users;

use bravedave\esse\json;
use bravedave\esse\page;
use bravedave\esse\request;

class controller extends \controller {

  protected function _index(): void {

    $this->title = config::label;

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('nav'))
      ->main()->then(fn () => $this->load('matrix'))
      ->aside()->then(fn () => $this->load('aside'))
      ->footer()->then(fn () => $this->load('footer'));
  }

  protected function before(): void {

    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
    config::users_checkdatabase();
    parent::before();
  }

  protected function postHandler(): void {

    $action = request::post('action');
    if ('get-by-id' == $action) {
      /*
        (_ => {
          $.post(_.url('users'), {
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

        if ($dto = (new dao\users)->getByID($id)) {

          Json::ack($action)
            ->add('data', $dto);
        } else {

          Json::nak($action);
        }
      } else {

        Json::nak($action);
      }
    } elseif ('get-matrix' == $action) {
      /*
        (_ => {
          $.post(_.url('users'),{action: 'get-matrix'})
            .then(d => {
              if ('ack' == d.response) {
                console.table(d.data);
              } else {
                _.growl(d);
              }
            });
        })(_esse_);
       */
      Json::ack($action)
        ->add('data', (new dao\users)->getMatrix());
    } elseif ('users-save' == $action) {

      $a = [
        'name' => (string)request::post('name'),
        'email' => (string)request::post('email'),
        'mobile' => (string)request::post('mobile'),
        'admin' => (int)request::post('admin'),
        'active' => (int)request::post('active')
      ];

      if ($password = (string)request::post('password')) {

        $a['password'] = password_hash($password, PASSWORD_DEFAULT);
      }

      if ($id = (int)request::post('id')) {

        (new dao\users)->UpdateByID($a, $id);
      } else {

        (new dao\users)->Insert($a);
      }

      json::ack($action);
    } else {

      parent::postHandler();
    }
  }

  public function edit($id = 0) {

    $this->data = (object)[
      'title' => $this->title = config::label_add,
      'dto' => new dao\dto\users
    ];

    if ($id = (int)$id) {

      $this->data->dto = (new dao\users)
        ->getByID($id);
      $this->data->title = config::label_edit;
    }

    $this->load('edit');
  }
}
