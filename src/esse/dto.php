<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse;

class dto {

  protected function _populate(?array $row = []) {

    foreach ($row as $k => $v) {

      $this->{$k} = $v;
    }
  }

  public function __construct(?array $row = []) {

    $this->_populate($row);
  }
}
