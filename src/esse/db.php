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

use config;

abstract class db {
  public bool $log = false;

  protected bool $_valid = false;

  public static function dbCheck(string $file) {
    return 'sqlite' == \config::$DB_TYPE ?
      new sqlite\dbCheck(config::dbi(), $file) :
      new mysql\dbCheck(config::dbi(), $file);
  }

  public static function dbTimeStamp(): string {

    return date("Y-m-d H:i:s", time());
  }

  public function escape(string $s): string {
    return $s;
  }

  public function fetchFields(string $table): array {

    return [];
  }

  public function fieldList(string $table): array {

    return [];
  }

  public function getCharSet(): string {
    return '';
  }

  public function Insert(string $table, array $a): int {
    return 0;
  }

  public function Q(string $sql) {

    return null;
  }

  public function quote(string $val): string {

    return sprintf("'%s'", $this->escape($val));
  }

  public function result(string $query): ?dbResult {

    return null;
  }

  public function Update(string $table, array $a, string $scope, bool $flushCache = true) {

    return null;
  }

  public function valid(): bool {

    return $this->_valid;
  }
}
