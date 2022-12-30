<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * using: https://www.scrapbook.cash/interfaces/key-value-store/
*/

namespace bravedave\esse;

use APCUIterator, MatthiasMullie\Scrapbook\Adapters\Apc;
use config;

class cache {
  protected static $_instance;
  protected $_cache;
  protected $ttl = 60;

  protected function __construct() {
    if (config::$DB_CACHE_DEBUG) logger::debug(__METHOD__);

    $this->ttl = config::$DB_CACHE_TTL;

    // create Scrapbook KeyValueStore object
    $this->_cache = new Apc;
  }

  /**
   * create a cache instance
   *
   * @return self
   */
  static function instance(): self {

    if (!self::$_instance) self::$_instance = new self;
    return self::$_instance;
  }

  /**
   * get a cache value
   *
   * @param string $key the key to retrieve
   *
   * @return mixed
   */
  public function get(string $key): mixed {

    if ($res = $this->_cache->get($key)) {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<get %s (hit)> %s', $key, __METHOD__));
    } elseif (config::$DB_CACHE_DEBUG) {

      logger::info(sprintf('<get %s (miss)> %s', $key, __METHOD__));
    }

    return $res;
  }

  /**
   * set a cache value
   *
   * @param string $key the key to set
   *
   * @param mixed $value the value to set
   *
   * @param int $ttl
   *
   * @return void
   */
  public function set(string $key, mixed $value, int $ttl = 0): void {

    if (!$ttl) $ttl = $this->ttl;

    if ($this->_cache->set($key, $value, $ttl)) {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<set %s> %s', $key, __METHOD__));
    }
  }

  /**
   * delete a cache value
   *
   * @param string $key the key to delete
   *
   * @param bool $wildcard use wilcard matching to delete many values
   *
   * @return bool
   */
  public function delete(string $key, bool $wildcard = false): bool {

    if ($wildcard) {

      $cachedKeys = new APCUIterator($key);
      $count = 0;
      $found = 0;
      foreach ($cachedKeys as $_key) {

        $found++;
        if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<wildard delete %s> <%s> %s', $key, $_key['key'], __METHOD__));
        if ($this->_cache->delete($_key['key'])) $count++;
      }

      if (config::$DB_CACHE_DEBUG && !$found) logger::debug(sprintf('<wildard delete %s> <nothing found> %s', $key, __METHOD__));

      return $found == $count;
    } else {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<delete %s> %s', $key, __METHOD__));
      return $this->_cache->delete($key);
    }

    return false;
  }

  /**
   * flush the cache
   *
   * @return void
   */
  public function flush(): void {

    if (config::$DB_CACHE_DEBUG || \config::$DB_CACHE_DEBUG_FLUSH) logger::debug(sprintf('<flush> : %s', __METHOD__));
    $this->_cache->flush();
  }
}
