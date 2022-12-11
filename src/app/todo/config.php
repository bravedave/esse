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

class config extends \config {  // noting: config extends global config classes
  const todo_db_version = 1;

  const label = 'Todo';  // general label for application

  static function todo_checkdatabase() {
    $dao = new dao\dbinfo;
    // $dao->debug = true;
    $dao->checkVersion('todo', self::todo_db_version);
  }
}
