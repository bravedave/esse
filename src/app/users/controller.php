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
      ->aside()->then(fn () => $this->load('aside'));
  }

  protected function before(): void {

    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
    config::users_checkdatabase();
    parent::before();
  }

  protected function postHandler() {

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

        $dao = new dao\users;
        if ($dto = $dao->getByID($id)) {

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
      $dao = new dao\users;
      Json::ack($action)
        ->add('data', $dao->getMatrix());
    } elseif ('users-save' == $action) {

      $a = [
        'description' => (string)request::post('description'),
        'complete' => (int)request::post('complete')
      ];

      $dao = new dao\users;
      if ($id = (int)request::post('id')) {

        $dao->UpdateByID($a, $id);
      } else {

        $dao->Insert($a);
      }

      json::ack($action);
    } else {

      parent::postHandler();
    }
  }

  public function edit($id = 0) {

    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\users
    ];

    if ($id = (int)$id) {

      $this->data->dto = (new dao\users)
        ->getByID($id);
      $this->data->title .= ' edit';
    }

    $this->load('edit');
  }
}
