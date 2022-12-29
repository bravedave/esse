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

use Parsedown;
use ReflectionMethod;
use RuntimeException;

/**
 * execute the instructions of the application
 */
class controller {

  protected array $paths = [];

  /**
   * by default, all controllers require validation
   * won't be active if authentitaction is not turned on
   *
   * @var bool $requireAuthentication
   */
  protected bool $requireAuthentication = true;

  protected array $viewPath = [];

  protected string $title = __CLASS__;

  protected string $route = '/';

  protected function _index(): void {

    print __METHOD__;
  }

  protected function before(): void {
  }

  protected function load(string $view): void {

    if ($path = $this->getView($view)) {

      if (strings::endswith($path, '.md')) {

        $fc = file_get_contents($path);
        printf('<div class="markdown-body">%s</div>', Parsedown::instance()->text($fc));
      } else {

        require $path;
      }
    } else {

      printf('view %s not found', $view);
    }
  }

  protected function getView(string $view, array $extensions = ['php', 'md']): string {

    foreach ($this->viewPath as $path) {

      // if (file_exists($p = sprintf('%s/%s.php', $path, $view))) return $p;
      foreach ($extensions as $ext) {

        if (file_exists($p = sprintf('%s/%s.%s', $path, $view, $ext))) return $p;
      }
    }

    foreach ($this->paths as $path) {

      // if (file_exists($p = sprintf('%s/views/%s.php', $path, $view))) return $p;
      foreach ($extensions as $ext) {

        if (file_exists($p = sprintf('%s/views/%s.%s', $path, $view, $ext))) return $p;
      }
    }
    return '';
  }

  protected function postHandler(): void {

    $action = request::post('action');
    json::nak($action);
  }

  public function __construct(array $paths) {

    $this->paths = $paths;
    $this->route = application::getRoute();
    // logger::info($this->route);

    $this->before();
  }

  public function __invoke(array $params): void {

    if ($params) {

      $method = $params[0];
      if (method_exists($this, $method)) {

        if ((new ReflectionMethod($this, $method))->isPublic()) {

          if ($p1 = $params[1] ?? null) {

            if ($p2 = $params[2] ?? null) {

              $this->{$method}($p1, $p2);
            } else {

              $this->{$method}($p1);
            }
          } else {

            $this->{$method}();
          }
        } else {

          logger::info(sprintf('<%s method is not public in %s> %s', $method, __CLASS__, __METHOD__));
          throw new RuntimeException('illegal access');
        }
      } elseif ($method) {

        if ($p1 = $params[1] ?? null) {

          if ($p2 = $params[2] ?? null) {

            $this->index($method, $p1, $p2);
          } else {

            $this->index($method, $p1);
          }
        } else {

          $this->index($method);
        }
      } else {

        $this->index();
      }
    } else {

      $this->index();
    }
  }

  public function index(): void {
    request::isPost() ?
      $this->postHandler() :
      $this->_index();
  }

  public function requiresAuthentication(): bool {

    return $this->requireAuthentication;
  }
}
