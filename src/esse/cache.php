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
    if (config::$DB_CACHE_DEBUG) logger::info(__METHOD__);

    $this->ttl = config::$DB_CACHE_TTL;

    // create Scrapbook KeyValueStore object
    $this->_cache = new Apc;
  }

  static function instance(): self {

    if (!self::$_instance) self::$_instance = new cache;
    return self::$_instance;
  }

  public function get(string $key) {
    if ($res = $this->_cache->get($key)) {

      if (config::$DB_CACHE_DEBUG) logger::info(sprintf('get(%s) (hit) : %s', $key, __METHOD__));
    } elseif (config::$DB_CACHE_DEBUG) {

      logger::info(sprintf('get(%s) (miss) : %s', $key, __METHOD__));
    }

    return $res;
  }

  public function set(string $key, $value, $ttl = null): void {

    if (!$ttl) $ttl = $this->ttl;

    if ($this->_cache->set($key, $value, $ttl)) {

      if (config::$DB_CACHE_DEBUG) logger::info(sprintf('set(%s) : %s', $key, __METHOD__));
    }
  }

  public function delete(string $key, $wildcard = false): void {

    if ($wildcard) {

      if (config::$DB_CACHE_DEBUG) logger::info(sprintf('wildard delete(%s) : %s', $key, __METHOD__));

      $cachedKeys = new APCUIterator($key);
      foreach ($cachedKeys as $_key) {

        if (config::$DB_CACHE_DEBUG) logger::info(sprintf('wildard delete(%s) => %s : %s', $key, $_key['key'], __METHOD__));
        $this->_cache->delete($_key['key']);
      }
    } else {

      if (config::$DB_CACHE_DEBUG) logger::info(sprintf('delete(%s) : %s', $key, __METHOD__));
      $this->_cache->delete($key);
    }
  }

  public function flush(): void {

    if (config::$DB_CACHE_DEBUG || \config::$DB_CACHE_DEBUG_FLUSH) logger::info(sprintf('<flush> : %s', __METHOD__));
    $this->_cache->flush();
  }
}
