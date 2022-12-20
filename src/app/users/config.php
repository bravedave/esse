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

class config extends \config {  // noting: config extends global config classes
  const users_db_version = 1;

  const label = 'Users';  // general label for application
  const label_add = 'add user';
  const label_edit = 'edit user';

  static function users_checkdatabase() {

    $dao = new dao\dbinfo;
    // $dao->debug = true;
    $dao->checkVersion('users', self::users_db_version);
  }
}
