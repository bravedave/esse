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

use bravedave\esse\{
  json,
  page,
  request, response,
  session
};
use config, controller, user;

/**
 * logon controller
 *
 *  [WARNING] this controller does not require Authentication
 */
class logon extends controller {
  /**
   * logon controller doesn't require Authentication
   * @var bool $requireAuthentication
   */
  protected bool $requireAuthentication = false;

  protected function _index(): void {

    $this->title = config::label_logon;

    (page::bootstrap())
      ->head($this->title)
      ->main()->then(fn () => $this->load('logon'));
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
