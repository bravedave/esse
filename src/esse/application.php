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

/**
 * the application class
 * sets up the application and instantiates a controller
 */
class application {

  protected static ?self $instance = null;

  protected array $paths = [
    'root' => __DIR__,
    'install' => __DIR__
  ];

  protected string $route = '';

  public function __construct(string $root = __DIR__) {

    self::$instance = $this;

    $this->paths['root'] = $root;

    config::initialize(); // after setting the root path ..

    $controller = implode('\\', [
      request::controller(),
      'controller'
    ]);

    if (class_exists($controller)) {

      $this->route = request::controller();
      $ctrl = new $controller($this->paths);

      if (method_exists($ctrl, $method = request::method())) {

        $ctrl->{$method}();
      } else {

        $ctrl->index();
      }
    } else {

      $essecontroller = implode('\\', [
        __NAMESPACE__,
        'controller',
        request::controller()
      ]);

      if (class_exists($essecontroller)) {

        $this->route = request::controller();
        $ctrl = new $essecontroller($this->paths);

        if (method_exists($ctrl, $method = request::method())) {

          $ctrl->{$method}();
        } else {

          $ctrl->index();
        }
      } else {

        printf('%s not found', $controller);
      }
    }

    // logger::debug('exit application : ' . __METHOD__);
  }

  public static function app(): ?self {

    return self::$instance;
  }

  public static function getRootPath(): string {

    return isset(self::$instance)  ?
      self::$instance->paths['root'] :
      '';
  }

  public static function getRoute(): string {

    return isset(self::$instance)  ?
      self::$instance->route :
      '';
  }

  protected static $_loaded_fallback = false;
  public static function load_esse_autoloader_fallback() {

    if (!self::$_loaded_fallback) {

      self::$_loaded_fallback = true;

      spl_autoload_register(function ($class): bool {

        if ($lib = realpath(implode([
          __DIR__,
          DIRECTORY_SEPARATOR,
          '..',
          DIRECTORY_SEPARATOR,
          'fallback',
          DIRECTORY_SEPARATOR,
          str_replace('\\', '/', $class),
          '.php'
        ]))) {

          include_once $lib;
          // logger::debug(sprintf('<lib: %s> %s', $lib, __METHOD__));
          return true;
        }

        return false;
      });
    }
  }
}

application::load_esse_autoloader_fallback();
