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

    if ('favicon.ico' == request::controller()) {

      response::serve(__DIR__ . '/resource/favicon.ico');
      return;
    }
    elseif ('application.drawio.svg' == request::controller()) {

      /**
       * special case for compatibility with Github
       */
      response::serve(__DIR__ . '/../../application.drawio.svg');
      return;
    }

    $controller = implode('\\', [
      request::controller(),
      'controller'
    ]);

    if (!class_exists($controller)) {

      if ($map = routes::map(request::controller())) {

        $controller = $map;
      }
    }

    if (!class_exists($controller)) {

      // esse controller
      $controller = implode('\\', [
        __NAMESPACE__,
        'controller',
        request::controller()
      ]);
    }

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

    if (class_exists($controller)) {

      $this->route = request::controller();
      $ctrl = new $controller($this->paths);

      if (!config::$AUTHENTICATION || !$ctrl->requiresAuthentication() || currentUser::isValid()) {

        $method = request::method();
        if (in_array(strtolower($method), $_protectedActions)) {

          logger::info(sprintf('protecting action %s => %s', $method, 'index'));
          $method = 'index';
        }

        $ctrl([
          $method,
          request::param1(),
          request::param2()
        ]);
      } else {

        /**
         * they are not valid,
         * if the controller requres authentication bump them to logon
         */
        $ctrl = new controller\logon($this->paths);
        $ctrl->index();
        logger::info(sprintf('<invalid user> <%s> %s', $controller, __METHOD__));
        return; // finito
      }
    } else {

      /**
       * fall back to the home controller to look
       * for a method of the controllers name
       */
      if ($method = request::controller()) {

        if (in_array(strtolower($method), $_protectedActions)) {

          printf('%s not found', $controller);
        } else {

          $controller = implode('\\', [
            'home',
            'controller'
          ]);

          if (!class_exists($controller)) {

            // esse controller
            $controller = implode('\\', [
              __NAMESPACE__,
              'home',
              request::controller()
            ]);
          }

          if (class_exists($controller)) {  // presumably it does, you could be fallen back to esse\controllers\home

            $this->route = 'home';
            $ctrl = new $controller($this->paths);

            if (!config::$AUTHENTICATION || !$ctrl->requiresAuthentication() || currentUser::isValid()) {

              $params = [$method];
              if ($p = request::method()) $params[] = $p;
              if ($p = request::param1()) $params[] = $p;
              if ($p = request::param2()) $params[] = $p;

              $ctrl($params);
            } else {

              /**
               * they are not valid,
               * if the controller requres authentication bump them to logon
               */
              $ctrl = new controller\logon($this->paths);
              $ctrl->index();
              logger::info(sprintf('<invalid user> <%s> %s', $controller, __METHOD__));
              return; // finito
            }
          }
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
  public static function load_esse_autoloader_fallback(): void {

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
