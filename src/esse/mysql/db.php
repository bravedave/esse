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

use bravedave\esse\db as esseDB;
use bravedave\esse\cache;
use bravedave\esse\dbResult as esse_dbResult;
use bravedave\esse\Exceptions\SQLException;
use bravedave\esse\Exceptions\UnableToSelectDatabase;
use bravedave\esse\logger;
use config;

class db extends esseDB {
  protected $mysqli, $dbname;

  protected static self $_instance = null;
  protected static int $dbiCount = 0;

  static function instance(): ?self {

    if (!self::$_instance) {

      if (config::$DB_TYPE == 'none' || config::$DB_TYPE == 'disabled') return null;

      self::$_instance = new self(
        config::$DB_HOST,
        config::$DB_NAME,
        config::$DB_USER,
        config::$DB_PASS
      );

      self::$dbiCount++;
      if (self::$dbiCount  > 1) {

        logger::info(sprintf('<db initialized (%s)> %s', self::$dbiCount, __METHOD__));
      }

      logger::debug(sprintf(
        '<db initialized (%s,%s,%s,%s)> %s',
        config::$DB_HOST,
        config::$DB_NAME,
        config::$DB_USER,
        config::$DB_PASS,
        __METHOD__
      ));
    }

    return self::$_instance;
  }

  public static function mysqli_field_type(int $type_id): string {
    static $types;

    if (!isset($types)) {
      $types = array();
      $constants = get_defined_constants(true);
      foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
    }

    return array_key_exists($type_id, $types) ? $types[$type_id] : "unKnown";
  }

  function __construct(string $host, string $database, string $user, string $pass) {
    $this->dbname = $database;
    $this->mysqli = @new \mysqli($host, $user, $pass, $database);

    if ($this->mysqli->connect_error) {
      logger::info(sprintf('\mysqli( %s, %s, ***, %s )',  $host, $user, $database));
      logger::info(sprintf('Connect Error (%s) %s', $this->mysqli->connect_errno, $this->mysqli->connect_error));
      throw new UnableToSelectDatabase;
    }

    $this->mysqli->set_charset('utf8');
    $this->_valid = true;
  }

  function __destruct() {

    if ($this->mysqli) {

      if ($a = $this->mysqli->error_list) {

        foreach ($a as $e) {

          logger::info(sprintf('<mysql-error : %s> %s', $e, __METHOD__));
        }
      }
      $this->mysqli->close();
      $this->mysqli = null;
    }
  }

  function __invoke(string $query): ?esse_dbResult {
    return $this->result($query);
  }

  public function affected_rows() {
    return ($this->mysqli->affected_rows);
  }

  public function dump() {
    if ($dbR = $this->result(sprintf('SHOW TABLES FROM %s', config::$DB_NAME))) {
      $uID = 0;
      while ($row = $dbR->fetch_row()) {
        printf(
          '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
          $uID,
          $row[0],
          PHP_EOL
        );
        printf(
          '<blockquote id="bqt%s" style="font-family: monospace; display: none;">%s',
          $uID++,
          PHP_EOL
        );

        /* Get field information for all columns */
        if ($res = $this->result(sprintf('SELECT * FROM `%s` LIMIT 1', $this->escape($row[0])))) {
          $finfo = $res->fetch_fields();

          foreach ($finfo as $val)
            printf('<br />%s %s (%s)', $val->name, $this->field_type($val->type), $val->length);
        }

        print "</blockquote>\n";
      }
    } else {
      printf(
        '<pre>
				DB Error, could not list tables
				MySQL Error: %s
				MySQL Host: %s
			</pre>',
        mysqli_error($this->mysqli),
        config::$DB_HOST
      );
    }
  }

  public function escape(string $s): string {
    return $this->mysqli->real_escape_string($s);
  }

  public function fetchFields(string $table) : array {
    $res = $this->Q("SELECT * FROM `$table` LIMIT 1");
    return ($res->fetch_fields());
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
      if (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_FLUSH) {

        foreach (debug_backtrace() as $e) {

          logger::info(sprintf('post flush: %s(%s)', $e['file'], $e['line']));
        }
      }
    }
  }

  public function field_exists($table, $field) {
    $ret = FALSE;

    $result = $this->Q("SHOW COLUMNS FROM $table");
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] == $field) {
          $ret = TRUE;
          break;
        }
      }
    }
    return ($ret);
  }

  public function field_type($v) {
    return (self::mysqli_field_type($v));
  }

  public function fieldList(string $table): array {

    $ret = [];
    $result = $this->Q("SHOW COLUMNS FROM `$table`");
    while ($row = mysqli_fetch_assoc($result))
      $ret[] = $row['Field'];

    return $ret;
  }

  public function getCharSet() : string {

    return $this->mysqli->character_set_name();
  }

  public function getDBName() {
    return $this->dbname;
  }

  public function Insert(string $table, array $a): int {
    $fA = [];
    $fV = [];
    foreach ($a as $k => $v) {
      $fA[] = $k;
      // $fV[] = $this->mysqli->real_escape_string($v);
      $fV[] = $this->quote($v);
    }

    $sql = sprintf(
      'INSERT INTO `%s`(`%s`) VALUES(%s)',
      $table,
      implode("`,`", $fA),
      implode(',', $fV)
    );

    $this->Q($sql);
    return $this->mysqli->insert_id;
  }

  public function Q($query) {

    if ($this->log) logger::sql($query);
    if ($result = $this->mysqli->query($query)) return $result;


    /****************************************
     * You are here because there was an error **/
    $message = sprintf(
      "Error : MySQLi : %s\nError : MySQLi : %s",
      $query,
      $this->mysqli->error
    );

    logger::sql($message);
    foreach (debug_backtrace() as $e) {

      logger::info(sprintf('%s(%s)', $e['file'] ?? '?file', $e['line'] ?? '?line'));
    }

    throw new \Exception($message);
  }

  public function quote(?string $val): string {
    return sprintf('"%s"', $this->escape($val));
  }

  public function result($query): ?dbResult {
    try {

      return new dbResult($this->Q($query), $this);
    } catch (\Exception $e) {

      throw new SQLException;
    }

    return null;
  }

  public function table_exists(string $table): bool {
    $sql = sprintf(
      'SELECT * FROM information_schema.tables WHERE table_schema = "%s" AND table_name = "%s" LIMIT 1',
      $this->escape($this->dbname),
      $this->escape($table)

    );

    if ($result = $this->result($sql)) {

      if ($dto = $result->dto()) return true;
    }

    return false;
  }

  public function Update(string $table, array $a, string $scope, bool $flushCache = true) {
    if ((bool)$flushCache) $this->flushCache();

    $aX = [];
    foreach ($a as $k => $v) {
      // $aX[] = "`$k` = '" . $this->mysqli->real_escape_string($v) . "'";
      $aX[] = sprintf('`%s` = %s', $k, $this->quote($v));
    }

    $sql = sprintf('UPDATE `%s` SET %s %s', $table, implode(', ', $aX), $scope);
    return $this->Q($sql);
  }
}
