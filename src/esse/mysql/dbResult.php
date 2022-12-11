<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\mysql;

use bravedave\esse\dbResult as esse_dbResult;

class dbResult extends esse_dbResult {
  protected $result = false;
  protected $db;

  public function __construct($result = null, $db = null) {

    if ($result) $this->result = $result;
    if ($db) $this->db = $db;
  }

  public function fetch(): array {

    return $this->result->fetch_assoc();
  }

  public function fetch_fields() {

    return $this->result->fetch_fields();
  }

  public function fetch_row() {

    return $this->result->fetch_row();
  }

  public function num_rows(): int {

    return $this->result->num_rows;
  }
}
