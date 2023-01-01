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

use bravedave\esse\Exceptions\{
  DatapathNotFound,
  DatapathNotWritable
};

abstract class config {

  const label = '_esse_';
  const label_logon = 'logon';

  const cssfiles = [
    __DIR__ . '/css/_esse_.css',
    __DIR__ . '/css/_esse_.markdown.css',
  ];

  const jsfiles = [
    __DIR__ . '/js/_esse_.js',
    __DIR__ . '/js/_esse_.browser.js',
    __DIR__ . '/js/_esse_.get.js',
    __DIR__ . '/js/_esse_.growl.js',
    __DIR__ . '/js/_esse_.randomString.js',
    __DIR__ . '/js/_esse_.serialize.js',
    __DIR__ . '/js/_esse_.table.js',
    __DIR__ . '/js/_esse_.url.js',
  ];

  const use_full_url = true;

  static protected ?string $_dataPath = null;

  static bool $AUTHENTICATION = true;
  static bool $CONTENT_SECURITY_ENABLED = true;

  static string $CIPHER = 'aes-128-cbc';
  static string $CRYPT_IV = ''; // 8 bit IV
  static string $CRYPT_KEY = ''; // 24 bit Key

  static string $DATE_FORMAT = 'Y-m-d';
  static string $DATE_FORMAT_LONG = 'D M d Y';
  static string $DATETIME_FORMAT = 'Y-m-d g:ia';
  static string $DATETIME_FORMAT_LONG = 'D M d Y g:ia';

  /**
   *	Caching using APCu, Interfaced through https://www.scrapbook.cash/
   * 	see dao
   *
   *	NOTE: If you enable this you need to have installed
   *		* APC => dnf install php-pecl-apcu
   *		* matthiasmullie/scrapbook => composer require matthiasmullie/scrapbook
   */
  static string $DB_CACHE = '';  // values = 'APC'
  static bool $DB_CACHE_DEBUG = false;
  static bool $DB_CACHE_DEBUG_FLUSH = false;
  static bool $DB_CACHE_DEBUG_TYPE_CONFLICT = true;
  static string $DB_CACHE_PREFIX = '';  // alphanumeric only, optionally create uniqueness for applications
  static int $DB_CACHE_TTL = 600; // 10 minutes

  static string $DB_HOST = 'localhost';
  static string $DB_TYPE = 'none';  // needs to be mysql or sqlite to run, disable with 'disabled'
  static string $DB_NAME = 'dbname';
  static string $DB_USER = 'dbuser';
  static string $DB_PASS = 'dbpass';
  static bool $DB_ALTER_FIELD_STRUCTURES = false;  // experimental

  static string $EMAILDOMAIN = 'example.tld';
  static bool $EMAIL_ERRORS_TO_SUPPORT = false;

  static int $JS_EXPIRE_TIME = 300;
  static int $JQUERY_EXPIRE_TIME = 900;
  static int $FONT_EXPIRE_TIME = 14400;
  static int $CSS_EXPIRE_TIME = 900;
  static $IMG_EXPIRE_TIME = 60;
  static int $CORE_IMG_EXPIRE_TIME = 60;  // set on images that come from the /image location

  static string $MAILDSN = '';
  static string $MAILSERVER = 'localhost';
  static string $MAILER = 'BrayWorth DVC Mailer 1.0.1 (https://brayworth.com/)';

  static string $TIME_FORMAT = 'g:ia';
  static string $TIMEZONE = 'UTC';

  static string $IMAP_AUTH_SERVER = '';

  static int $SESSION_CACHE_EXPIRE = 180;

  static string $SUPPORT_EMAIL = 'help@example.tld';

  static string $SUPPORT_NAME = 'Help Master';

  static string $THEME = '';

  static string $WEBNAME = 'Esse';
  static string $SLOGAN = 'MVC in PHP';

  static string $UMASK = '0022';

  static public function checkDBconfigured(): bool {

    if (self::$DB_TYPE == 'mysql' || self::$DB_TYPE == 'sqlite' || self::$DB_TYPE == 'disabled')
      return true;

    return false;
  }

  /**
   * the default location for storing data
   *
   * @return string
   */
  static public function dataPath(): string {

    if (\is_null(self::$_dataPath)) {

      $root = sprintf('%s', dirname(application::getRootPath()));
      $datapath = sprintf('%s%sdata', $root, DIRECTORY_SEPARATOR);

      /**
       * the location of the datapath can be changed by
       * including a 'datapath' file in the default
       * datapath location
       */
      if (is_dir($datapath) && file_exists($_redir_file = $datapath . '/datapath')) {

        $_redir = file_get_contents($_redir_file);
        if (is_dir($_redir) && is_writable($_redir)) $datapath = $_redir;
      }

      self::$_dataPath = $datapath;
      if (is_writable($root) || is_writable(self::$_dataPath)) {

        if (!is_dir(self::$_dataPath)) mkdir(self::$_dataPath);
        if (!file_exists($readme = self::$_dataPath . DIRECTORY_SEPARATOR . 'readme.txt')) {

          file_put_contents($readme, implode(PHP_EOL, [
            '-----------',
            'data Folder',
            '-----------',
            '',
            'keep this folder private',
            '',
            '--------------------------------------------',
            '*-* DO NOT UPLOAD TO A PUBLIC REPOSITORY *-*',
            '--------------------------------------------'
          ]));
        }

        if (!file_exists($ignore = self::$_dataPath . DIRECTORY_SEPARATOR . '.gitignore')) file_put_contents($ignore, '*');
        if (!is_dir(self::$_dataPath)) throw new DatapathNotFound(self::$_dataPath);

        return self::$_dataPath;
      }

      throw new DatapathNotWritable(self::$_dataPath);
    }

    return self::$_dataPath;
  }

  public static function dbCachePrefix(): string {

    if (self::$DB_CACHE_PREFIX) {

      return self::$DB_CACHE_PREFIX;
    } elseif ('mysql' == self::$DB_TYPE) {

      return str_replace('.', '_', self::$DB_HOST . '_' . self::$DB_NAME);
    } else {

      /**
       * it's probably sqlite, so we need a unique prefix for this database
       *
       * this could require further development if we are going to support
       * multiple cached sqlite databases in the same application, otherwise
       * this database, this appication is unique
       * */
      $path = implode(DIRECTORY_SEPARATOR, [
        self::dataPath(),
        'dbCachePrefix.json'
      ]);

      if (\file_exists($path)) {

        $j = \json_decode(\file_get_contents($path));
        self::$DB_CACHE_PREFIX = $j->prefix;
        return self::$DB_CACHE_PREFIX;
      } else {

        $a = (object)['prefix' => bin2hex(random_bytes(6))];
        \file_put_contents($path, \json_encode($a));
        self::$DB_CACHE_PREFIX = $a->prefix;
        return self::$DB_CACHE_PREFIX;
      }
    }
  }

  protected static $_dbi = null;
  public static function dbi(): db {

    if (is_null(self::$_dbi)) {

      if ('sqlite' == config::$DB_TYPE) {

        self::$_dbi = sqlite\db::instance();
      } else {

        self::$_dbi = mysql\db::instance();
      }
    }

    return (self::$_dbi);
  }

  static protected function defaultsPath(): string {

    return implode(DIRECTORY_SEPARATOR, [
      self::dataPath(),
      'esse-defaults.json'
    ]);
  }

  static public function initialize(): void {
    /**
     * config initialize is called in _application->__construct()
     *
     * This is a local overwrite of the db parameters
     */
    $path = self::defaultsPath();
    if (file_exists($path)) {
      $_a = [
        'authentication' => self::$AUTHENTICATION,
        'crypt_iv' => self::$CRYPT_IV,
        'crypt_key' => self::$CRYPT_KEY,
        'db_type' => self::$DB_TYPE,
        'db_host' => self::$DB_HOST,
        'db_name' => self::$DB_NAME,
        'db_user' => self::$DB_USER,
        'db_pass' => self::$DB_PASS,
        'db_cache' => self::$DB_CACHE,
        'db_cache_debug' => self::$DB_CACHE_DEBUG,
        'db_cache_debug_flush' => self::$DB_CACHE_DEBUG_FLUSH,
        'db_cache_debug_type_conflict' => self::$DB_CACHE_DEBUG_TYPE_CONFLICT,
        'date_format' => self::$DATE_FORMAT,
        'datetime_format' => self::$DATETIME_FORMAT,
        'email_errors_to_support' => self::$EMAIL_ERRORS_TO_SUPPORT,
        'emaildomain' => self::$EMAILDOMAIN,
        'imap_auth_server' => self::$IMAP_AUTH_SERVER,
        'maildsn' => self::$MAILDSN,
        'session_cache_expire' => self::$SESSION_CACHE_EXPIRE,
        'support_name' => self::$SUPPORT_NAME,
        'support_email' => self::$SUPPORT_EMAIL,
        'timezone' => self::$TIMEZONE,
        'theme' => self::$THEME,
        'umask' => self::$UMASK,
        'webname' => self::$WEBNAME,
      ];

      $a = (object)array_merge($_a, (array)json_decode(file_get_contents($path)));

      self::$AUTHENTICATION = $a->authentication;

      self::$CRYPT_IV = $a->crypt_iv;
      self::$CRYPT_KEY = $a->crypt_key;

      self::$DB_TYPE = $a->db_type;
      self::$DB_HOST = $a->db_host;
      self::$DB_NAME = $a->db_name;
      self::$DB_USER = $a->db_user;
      self::$DB_PASS = $a->db_pass;
      self::$DB_CACHE = $a->db_cache;
      self::$DB_CACHE_DEBUG = $a->db_cache_debug;
      self::$DB_CACHE_DEBUG_FLUSH = $a->db_cache_debug_flush;
      self::$DB_CACHE_DEBUG_TYPE_CONFLICT = $a->db_cache_debug_type_conflict;
      self::$DATE_FORMAT = $a->date_format;
      self::$DATETIME_FORMAT = $a->datetime_format;

      self::$EMAIL_ERRORS_TO_SUPPORT = $a->email_errors_to_support;
      self::$EMAILDOMAIN = $a->emaildomain;

      self::$IMAP_AUTH_SERVER = $a->imap_auth_server;
      self::$MAILDSN = $a->maildsn;

      self::$SESSION_CACHE_EXPIRE = $a->session_cache_expire;
      self::$SUPPORT_NAME = $a->support_name;
      self::$SUPPORT_EMAIL = $a->support_email;

      self::$TIMEZONE = $a->timezone;
      self::$THEME = $a->theme;

      self::$UMASK = $a->umask;

      self::$WEBNAME = $a->webname;
    } else {

      $path = implode(DIRECTORY_SEPARATOR, [
        self::dataPath(),
        'esse-defaults-sample.json'
      ]);

      $pathSQL = implode(DIRECTORY_SEPARATOR, [
        self::dataPath(),
        'esse-mariadb-setup-example.sql'
      ]);

      if (!file_exists($path)) {

        $a = [
          'authentication' => true,
          'crypt_iv' => strings::random_string(16),
          'crypt_key' => strings::random_string(24),
          'db_type' => 'sqlite',
          'db_host' => self::$DB_HOST,
          'db_name' => self::$DB_NAME,
          'db_user' => self::$DB_USER,
          'db_pass' => self::$DB_PASS,
          'db_cache' => self::$DB_CACHE,
          'db_cache_debug' => self::$DB_CACHE_DEBUG,
          'db_cache_debug_flush' => self::$DB_CACHE_DEBUG_FLUSH,
          'db_cache_debug_type_conflict' => self::$DB_CACHE_DEBUG_TYPE_CONFLICT,
          'date_format' => 'd/m/Y',
          'datetime_format' => 'd/m/Y g:ia',
          'email_errors_to_support' => self::$EMAIL_ERRORS_TO_SUPPORT,
          'emaildomain' => self::$EMAILDOMAIN,
          'imap_auth_server' => self::$IMAP_AUTH_SERVER,
          'maildsn' => 'smtp://mail:25?verify_peer=0',
          'session_cache_expire' => self::$SESSION_CACHE_EXPIRE,
          'timezone' => self::$TIMEZONE,
          'theme' => self::$THEME,
          'support_name' => self::$SUPPORT_NAME,
          'support_email' => self::$SUPPORT_EMAIL,
          'umask' => self::$UMASK,
          'webname' => self::$WEBNAME,
        ];

        file_put_contents($path, json_encode($a, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
      }

      if (!file_exists($pathSQL)) {

        file_put_contents($pathSQL, sprintf(
          "CREATE DATABASE IF NOT EXISTS `%s`;\n" .
            "GRANT ALL ON `%s`.* TO '%s'@'%s' IDENTIFIED BY '%s';\n" .
            "FLUSH PRIVILEGES;",
          self::$DB_NAME,
          self::$DB_NAME,
          self::$DB_USER,
          request::getServerIP(),
          self::$DB_PASS
        ));
      }
    }

    if (self::$DB_CACHE == 'APC') {

      $apcuAvailabe = function_exists('apcu_enabled') && apcu_enabled();
      if (!$apcuAvailabe) {

        logger::info(sprintf('<WARNING : APCu enabled but not available - disabling> %s', __METHOD__));
        self::$DB_CACHE = '';
      }
    }
  }

  protected static $_options = [];

  protected static function optionsPath(): string {
    return implode(DIRECTORY_SEPARATOR, [
      self::dataPath(),
      'esse-options.json'
    ]);
  }

  public static function option(string $key, null|string $val = null): string {
    $debug = false;
    // $debug = true;

    if (!self::$_options) {

      if (file_exists($config = self::optionsPath())) {

        self::$_options = (array)json_decode(file_get_contents($config));
      }
    }

    $ret = '';
    if (self::$_options) {

      /* return the existing value */
      if (isset(self::$_options[$key])) {

        $ret = (string)self::$_options[$key];
        if ($debug) logger::debug(sprintf('retrieve option value : %s = %s', $key, $ret));
      } else {

        if ($debug) logger::debug(sprintf('retrieve option value (default - not set) : %s = %s', $key, $ret));
      }
    } else {

      if ($debug) logger::debug(sprintf('retrieve option value (null): %s = %s', $key, $ret));
    }

    if (!is_null($val)) {

      /* writer */
      if ((string)$val == '') {

        if (isset(self::$_options[$key])) unset(self::$_options[$key]);
      } else {

        self::$_options[$key] = (string)$val;
      }

      file_put_contents(
        self::optionsPath(),
        json_encode(
          self::$_options,
          JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        )
      );
    }

    return ($ret);
  }

  /**
   * return a writable path with a trailing slash
   */
  static public function tempdir(): string {

    $dir = rtrim(sys_get_temp_dir(), '/\\');
    return ($dir . DIRECTORY_SEPARATOR);
  }
}
