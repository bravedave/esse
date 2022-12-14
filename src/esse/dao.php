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
use RuntimeException;

abstract class dao {
  protected string $_sql_getByID = 'SELECT * FROM %s WHERE id = %d';
  protected string $_sql_getAll = 'SELECT %s FROM %s %s';

  protected string $_db_name = '';
  protected string $_db_cache_prefix = '';
  protected string $template = '';

  public db $db;
  public bool $log = false;

  function __construct(db $db = null) {

    if (!config::checkDBconfigured()) {

      logger::info(sprintf('<DB not configured> %s', __METHOD__));
      throw new RuntimeException('DB not configured');
    }

    $this->db = is_null($db) ? config::dbi() : $db;

    $this->TableChecks();
    $this->before();
  }

  public function __invoke(int $id): ?dto {

    if ($dto = $this->getByID($id)) {
      if (method_exists($this, 'getRichData')) {

        return $this->getRichData($dto);
      }

      return $dto;
    }

    return null;
  }

  public static function asDTO($res, string $template = ''): array {

    return $res->dtoSet(null, $template);
  }

  protected function before() {
    /*
		* Abstract method placeholder for use by the child class.
		* This method is called at the end of __construct()
		*
		* avoid replacing the default __construct method - use before instead
		*
		* Inspired by something I read in the fuelPHP documentation
		* this method is called at the end of __construct and can
		* be used to modify the _controller class
		*/
  }

  protected function cacheKey(int $id, string $field = ''): string {

    if ($field) {

      return sprintf(
        '%s_%s_%s',
        $this->db_name(),
        $id,
        $field
      );
    } else {

      return sprintf(
        '%s_%s',
        $this->db_name(),
        $id
      );
    }
  }

  protected function cacheKey_delete(int $id, string $field = ''): string {

    return sprintf('/%s/', $this->cacheKey($id, $field));
  }

  protected function cachePrefix(): string {

    if ($this->_db_cache_prefix) return config::dbCachePrefix() . $this->_db_cache_prefix;
    return config::dbCachePrefix();
  }

  protected ?cache $_cache_instance = null;
  protected function cache(): ?cache {

    if (config::$DB_CACHE == 'APC') {

      if (!$this->_cache_instance) $this->_cache_instance = cache::instance($this->cachePrefix());
      return $this->_cache_instance;
    }

    return null;
  }

  protected function check() {

    if ($dbc = $this->structure()) return $dbc->check();
    return false;
  }

  protected function _create() {

    if ('sqlite' == config::$DB_TYPE) {

      $fieldList = $this->db->fieldList($this->db_name());
      $o = new dto;
      foreach ($fieldList as $f) {
        $o->{$f->name} = $f->dflt_value;
      }

      return $o;
    }

    $res = $this->Result(sprintf('SHOW COLUMNS FROM %s', $this->db_name()));
    $dtoSet = $res->dtoSet(function ($dto) {

      /*
				in:

				[Field] => id
				[Type] => bigint(20)
				[Null] => NO
				[Key] => PRI
				[Default] =>
				[Extra] => auto_increment
			*/

      //~ $field->Dec
      $dto->Len = 0;
      $type = strtoupper(preg_replace('@\(.*$@', '', $dto->Type));

      if ('BIGINT' == $type || 'SMALLINT' == $type || 'TINYINT' == $type || 'INT' == $type) {

        $dto->Len = trim(preg_replace('@^.*\(@', '', $dto->Type), ') ');
        $dto->Type = $type;
        $dto->Default = (int)$dto->Default;
      } elseif ('DATE' == $type || 'DATETIME' == $type) {

        $dto->Type = $type;
      } elseif ('MEDIUMTEXT' == $type || 'TEXT' == $type) {

        $dto->Type = $type;
      } elseif ('VARCHAR' == $type || 'VARBINARY' == $type) {

        $dto->Len = trim(preg_replace('@^.*\(@', '', $dto->Type), ') ');
        $dto->Type = $type;
      }

      return $dto;
    });

    $o = new dto;
    foreach ($dtoSet as $dto) $o->{$dto->Field} = $dto->Default;
    return $o;
  }

  public function cacheDelete(int $id): void {

    if ($cache = $this->cache()) {

      $key = $this->cacheKey_delete($id);
      if (!$cache->delete($key, $wildcard = true)) logger::debug(sprintf('<failed to delete %s> %s', $key, __METHOD__));
    }
  }

  public function create() {    /* returns a new dto of the file */

    if ($this->template) return $this->_create();
    return new $this->template;
  }

  public function count(): int {
    if (!$this->_db_name) throw new Exceptions\DBNameIsNull;

    if ($res = $this->Result(sprintf('SELECT COUNT(*) as i FROM `%s`', $this->_db_name))) {

      if ($dto = $res->dto()) return $dto->i;
    }

    return 0;
  }

  public function db_name(): string {

    if ($this->_db_name) return $this->_db_name;
    return '';
  }

  public static function dbTimeStamp(): string {

    return db::dbTimeStamp();
  }

  public function delete($id): void {
    if (!$this->_db_name) throw new Exceptions\DBNameIsNull;

    $this->db->log = $this->log;
    $this->Q(sprintf('DELETE FROM %s WHERE id = %d', $this->_db_name, (int)$id));

    $this->cacheDelete($id);
  }

  public function dtoSet(dbResult $res, callable $func = null): array {

    return $res->dtoSet($func, $this->template);
  }

  public function escape($s): string {

    return $this->db->escape($s);
  }

  public function getAll($fields = '*', $order = '') {
    if (!$this->_db_name) throw new Exceptions\DBNameIsNull;

    $this->db->log = $this->log;
    return ($this->Result(sprintf($this->_sql_getAll, $fields, $this->db_name(), $order)));
  }

  public function getByID($id) {

    if (!$this->_db_name) throw new Exceptions\DBNameIsNull;

    if ($cache = $this->cache()) {

      $key = $this->cacheKey($id);
      if ($dto = $cache->get($key)) {

        /**
         * The problem is there are some dirty unserializable dto's,
         * particularly in CMS (private repository) which is very old code
         *
         * so, check the type matches ..
         * debug is currently on for this => config::$DB_CACHE_DEBUG_TYPE_CONFLICT = true;
         *
         */
        if ($thisType = get_class($dto)) {

          $thisType = $thisType; // namespace will have preceding \, get_class will come from root
          $approvedType = ltrim($this->template ? $this->template : __NAMESPACE__ . '\dto\dto', '\\');
          if ($thisType == $approvedType) {

            if (config::$DB_CACHE_DEBUG) logger::info(sprintf('<type check %s> <%s> %s', $thisType, $approvedType, __METHOD__));
            return $dto;
          } elseif (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_TYPE_CONFLICT) {

            logger::info(sprintf('<fails type check %s> <%s> %s', $thisType, $approvedType, __METHOD__));
          }
        } elseif (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_TYPE_CONFLICT) {

          logger::info(sprintf('<cached object has no type> %s', __METHOD__));
        }
      }
    } else {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<cache not enabled> %s', __METHOD__));
    }

    $this->db->log = $this->log;
    if ($res = $this->Result(sprintf($this->_sql_getByID, $this->_db_name, (int)$id))) {

      if ($dto = $res->dto($this->template)) {

        if (config::$DB_CACHE == 'APC') $cache->set($key, $dto);
      }

      return ($dto);
    }

    return false;
  }

  public function getFieldByID($id, $fld) {

    if (!$this->_db_name) throw new Exceptions\DBNameIsNull;

    if ($cache = $this->cache()) {

      $key = $this->cacheKey($id, $fld);
      if ($v = $cache->get($key)) return ($v);
    }

    $this->db->log = $this->log;
    if ($res = $this->Result(sprintf($this->_sql_getByID, $this->_db_name, (int)$id))) {

      if ($dto = $res->dto($this->template)) {

        if (config::$DB_CACHE == 'APC') $cache->set($key, $dto->{$fld});
        return ($dto->{$fld});
      }
    }

    return false;
  }

  public function getRichData(dto $dto): ?dto {

    return $dto;
  }

  public function Insert(array $a): int {

    if (is_null($this->db_name())) throw new Exceptions\DBNameIsNull;

    if (isset($a['id'])) {
      unset($a['id']);
    }

    $this->db->log = $this->log;
    return $this->db->Insert($this->db_name(), $a);
  }

  public function Update($a, $condition, $flushCache = true) {

    if (is_null($this->db_name())) throw new Exceptions\DBNameIsNull;

    $this->db->log = $this->log;
    return $this->db->Update($this->db_name(), $a, $condition, $flushCache);
  }

  public function UpdateByID($a, $id) {

    if (is_null($this->db_name())) throw new Exceptions\DBNameIsNull;

    $this->cacheDelete($id);
    return $this->Update($a, sprintf('WHERE id = %d', $id), $flushCache = false);
  }

  /**
   * runs a query and returns a dbResult object
   * the opbject maybe a dvc\dbResult or a dvc\sqlite\dbResult
   */
  public function Result($query) {

    $this->db->log = $this->log;
    return $this->db->Result($query);
  }

  public function Q($query) {

    $this->db->log = $this->log;
    return $this->db->Q($query);
  }

  public function quote($s): string {

    return $this->db->quote($s);
  }

  protected function structure($name = null) {

    return false;
  }

  protected function TableChecks(): void {

    if (!$this->db->valid()) return;

    if (!$this->_db_name) return;

    if (!($this->TableExists())) $this->check();
  }

  protected function TableExists(null|string $table = null): bool {

    if (is_null($table)) $table = $this->db_name();
    if (!$table) return false;

    if ('sqlite' == config::$DB_TYPE) {

      $sql = sprintf(
        'SELECT `name` FROM `sqlite_master` WHERE `type` = %s AND `name` = %s',
        $this->quote('table'),
        $this->quote($table)
      );

      if ($res = $this->Result($sql)) {

        return (bool)$res->dto();
      }
    } else {

      $sql = sprintf(
        'SELECT
          CASE WHEN (
            SELECT
              COUNT(*)
            FROM
              information_schema.TABLES
            WHERE
              TABLE_SCHEMA = %s
              AND TABLE_NAME = %s
            ) < 1 THEN 1
          ELSE 0
          END t',
        $this->quote('DATABASENAME'),
        $this->quote($table)
      );

      if ($res = $this->Result($sql)) {

        if ($row = $res->fetch()) {

          if ($row['t'] == 1) return true;
        }
      }
    }

    return false;
  }
}
