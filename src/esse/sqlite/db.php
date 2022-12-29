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

use bravedave\esse\db as esseDB;
use bravedave\esse\cache;
use bravedave\esse\dbResult as esse_dbResult;
use bravedave\esse\logger;
use config, ZipArchive;
use SQLite3;

class db extends esseDB {
  protected null|SQLite3 $_db;
  protected string $_path;

  protected static null|self $_instance = null;

  static function instance(): self {

    if (!self::$_instance) self::$_instance = new self;
    return self::$_instance;
  }

  protected function __construct() {
    $this->_path = sprintf('%s%ssqlite.db', config::dataPath(), DIRECTORY_SEPARATOR);
    if (file_exists($this->_path)) {

      $this->_db = new SQLite3($this->_path);  // throws exception on failure

    } else {

      // I prefer this naming convention because in windows you can associate the extension
      $this->_path = sprintf('%s%sdb.sqlite', config::dataPath(), DIRECTORY_SEPARATOR);
      $this->_db = new SQLite3($this->_path);  // throws exception on failure
    }

    if ($this->_db) $this->_db->busyTimeout(6000);  // 6 seconds
    $this->_valid = true;
  }

  public function __destruct() {

    if ($this->_db) $this->_db->close();
    $this->_db = null;
  }

  function __invoke(string $query): ?esse_dbResult {
    return $this->result($query);
  }

  public function dump(): void {

    if ($tables = $this->tables()) {

      $uID = 0;
      foreach ($tables as $table) {
        printf(
          '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
          $uID,
          $table,
          PHP_EOL
        );
        printf(
          '<blockquote id=\'bqt%s\' style="font-family: monospace; display: none;">%s',
          $uID++,
          PHP_EOL
        );

        /* Get field information for all columns */
        if ($fields = $this->fieldList($table)) {
          foreach ($fields as $field) {
            printf('<br />%s %s %s', $field->name, $field->type, ($field->pk ? 'primary key' : ''));
          }
        }

        print "</blockquote>\n";
      }
    }
  }

  public function escape(string $value): string {
    return $this->_db->escapeString($value);
  }

  public function flushCache() {

    if (config::$DB_CACHE == 'APC') {
      /**
       * the automatic caching is controlled by:
       *	=> \dao\_dao->getByID addes to cache
       *  => \dao\_dao->UpdateByID flushes the cache selectively
       *		 - and sets flushCache to FALSE - so you won't be here
       *
       *	if you are here it is because Update was called casually outside
       *	of UpdateByID <=> a master flush is required
       */
      $cache = cache::instance();
      $cache->flush();
    }
  }

  public function field_exists($table, $field) {
    $ret = false;

    $fieldList = $this->fieldList($table);
    foreach ($fieldList as $f) {
      if ($field === $f->name) {
        return true;
        break;  // never executes

      }
    }

    return ($ret);
  }

  public function fieldList(string $table): array {

    $ret = [];
    if ($result = $this->result(sprintf('PRAGMA table_info(%s)', $table))) {

      while ($dto = $result->dto())
        $ret[] = $dto;
    }

    return $ret;
  }

  public function getPath() {
    return ($this->_path);
  }

  public function Insert(string $table, array $a): int {
    /**
     * Insert values into SQLite table
     *
     * Note: SQLite values must delimit with ' (single quote)
     *
     * Parameters: 	Table to update
     * 				array of key => values
     *
     */
    $fA = [];
    $fV = [];
    foreach ($a as $k => $v) {
      $fA[] = $k;
      $fV[] = $this->escape($v);
    }

    $sql = sprintf("INSERT INTO `%s`(`%s`) VALUES('%s')", $table, implode("`,`", $fA), implode("','", $fV));

    $this->_db->exec($sql);
    return $this->_db->lastInsertRowID();
  }

  public function Q(string $sql) {

    if ($this->log) logger::sql($sql);
    try {

      if ($result = $this->_db->query($sql)) return ($result);
    } catch (\Throwable $th) {
      /****************************************
       * You are here because there was an error **/
      $message = sprintf(
        "Error : SQLite : %s\nError : SQLite : %s",
        $sql,
        $this->_db->lastErrorMsg()
      );

      logger::sql($sql);
      foreach (debug_backtrace() as $e)
        logger::info(sprintf('%s(%s)', $e['file'], $e['line']));

      throw new \Exception($message);
    }
  }

  public function quote(string $val): string {

    return sprintf("'%s'", $this->escape($val));
  }

  public function result(string $query): esse_dbResult {

    return new dbResult($this->Q($query), $this);
  }

  public function tables(): array {

    $ret = [];
    if ($result = $this->result("SELECT name FROM sqlite_master WHERE type='table'")) {
      while ($dto = $result->dto()) {
        if (!preg_match('/^sqlite_/', $dto->name))
          $ret[] = $dto->name;
      }
    }

    return $ret;
  }

  public function table_exists(string $table): bool {
    $sql = sprintf(
      "SELECT name FROM sqlite_master WHERE type='table' and name='%s'",
      $this->escape($table)

    );

    if ($result = $this->result($sql)) {
      if ($dto = $result->dto()) {
        return true;
      }
    }

    return (false);
  }

  public function Update(string $table, array $a, string $scope, bool $flushCache = true) {
    if ((bool)$flushCache) $this->flushCache();

    /**
     * Update values into SQLite table
     *
     * Note: SQLite values must delimit with ' (single quote)
     *
     * Parameters: 	Table to update
     * 				array of key => values
     * 				scope of update : e.g. 'WHERE id = 1'
     */
    $aX = [];
    foreach ($a as $k => $v)
      $aX[] = "`$k` = '" . $this->escape($v) . "'";

    $sql = sprintf('UPDATE `%s` SET %s %s', $table, implode(', ', $aX), $scope);
    return ($this->Q($sql));
  }

  public function valid(): bool {

    if (!self::$_instance) self::$_instance = new self;

    if (self::$_instance) return true;

    return false;
  }

  public function zip() {
    $debug = false;
    // $debug = TRUE;

    $zip = new ZipArchive();
    $filename = sprintf('%s%sdb.zip', config::dataPath(), DIRECTORY_SEPARATOR);

    if (file_exists($filename)) {
      unlink($filename);
    }

    if ($debug) logger::info(sprintf('sqlite\db->zip() : <%s>', $filename));

    if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {

      logger::info(sprintf('sqlite\db->zip() : cannot open <%s>', $filename));
    } else {

      if ($debug) logger::info(sprintf('sqlite\db->zip() : adding <%s>', $this->_path));
      $zip->addFile($this->_path, 'db.sqlite');

      if ($debug) logger::info(sprintf('sqlite\db->zip() : numfiles : %s', $zip->numFiles));
      if ($debug) logger::info(sprintf('sqlite\db->zip() : status : %s', $zip->status));

      $zip->close();

      return ($filename);
    }
  }
}
