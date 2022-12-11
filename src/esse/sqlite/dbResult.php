<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\sqlite;

use bravedave\esse\dbResult as esse_dbResult;

class dbResult extends esse_dbResult {
  protected $result = false;
  protected $db;

  public function __construct($result = null, $db = null) {
    if ($result) $this->result = $result;
    if ($db) $this->db = $db;
  }

  public function fetch(): array {

    if ($ret = $this->result->fetchArray(SQLITE3_ASSOC)) {

      return $ret;
    }

    return [];
  }
}
