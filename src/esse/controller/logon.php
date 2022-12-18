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

use bravedave\esse\json;
use bravedave\esse\page;
use bravedave\esse\request;
use bravedave\esse\response;
use bravedave\esse\session;
use config, controller, user;

class logon extends controller {
  /**
   * logon controller doesn't require validation
   * @var bool $requireValidation
   */
  protected bool $requireValidation = false;

  protected function _index(): void {

    $this->title = config::label_logon;

    (page::bootstrap())
      ->head($this->title)
      ->body()->then(fn () => $this->load('logon'));
  }

  protected function postHandler(): void {

    $action = request::post('action');
    if ('-system-logon-' == $action) {

      if ($u = (string)request::post('u')) {

        if ($p = request::post('p')) {

          if ($user = user::authenticate($u, $p)) {

            if ($user->isValid()) {

              json::ack($action);
            } else {

              json::nak($action);
            }
          } else {

            json::nak($action);
          }
        } else {

          json::nak($action);
        }
      } else {

        json::nak($action);
      }
    } else {

      parent::postHandler();
    }
  }

  public function logoff() {

    session::destroy();
    response::redirect();
  }
}