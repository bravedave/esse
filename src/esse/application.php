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

use config, currentUser;
use ReflectionMethod;
use RuntimeException;

// use controller;

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

  protected bool $service = false;

  protected function __construct(string $root = __DIR__) {

    self::$instance = $this;

    $this->paths['root'] = $root;

    config::initialize(); // after setting the root path ..

    if ($this->service) return; // this is being called from the command line

    $controller = implode('\\', [
      request::controller(),
      'controller'
    ]);

    if (!class_exists($controller)) {

      // esse controller
      $controller = implode('\\', [
        __NAMESPACE__,
        'controller',
        request::controller()
      ]);
    }

    if (class_exists($controller)) {

      $this->route = request::controller();
      $ctrl = new $controller($this->paths);

      if (!config::$AUTHENTICATION || currentUser::isValid() || !$ctrl->requiresAuthentication()) {

        // logger::debug(sprintf('<valid user : %s> %s', currentUser::id(), __METHOD__));
        /**
         * Quiet Security - some actions are protected
         * from outside calling, don't broadcast the error
         */
        $_protectedActions = [
          '__construct',
          '__destruct',
          'application',
          'authorize',
          'before',
          'load'
        ];

        $method = request::method();
        if (in_array(strtolower($method), $_protectedActions)) {

          logger::info(sprintf('protecting action %s => %s', $method, 'index'));
          $method = 'index';
        }

        if (method_exists($ctrl, $method = request::method())) {

          if ((new ReflectionMethod($ctrl, $method))->isPublic()) {

            if ($p1 = request::param1()) {

              if ($p2 = request::param2()) {

                $ctrl->{$method}($p1, $p2);
              } else {

                $ctrl->{$method}($p1);
              }
            } else {

              $ctrl->{$method}();
            }
          } else {

            logger::info(sprintf('<%s method is not public in %s> %s', $method, $controller, __METHOD__));
            throw new RuntimeException('illegal access');
          }
        } elseif ($method) {

          if ($p1 = request::param1()) {

            if ($p2 = request::param2()) {

              $ctrl->index($method, $p1, $p2);
            } else {

              $ctrl->index($method, $p1);
            }
          } else {

            $ctrl->index($method);
          }
        } else {

          $ctrl->index();
        }
      } else {

        // they are not valid,
        // if the controller requres authentication bump them to logon
        $ctrl = new controller\logon($this->paths);
        $ctrl->index();
        logger::info(sprintf('<invalid user> %s', __METHOD__));
        return; // finito
      }
    } else {

      printf('%s not found', $controller);
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
