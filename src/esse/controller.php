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

      require $path;
    } else {

      printf('view %s not found', $view);
    }
  }

  protected function getView(string $view): string {

    foreach ($this->viewPath as $path) {

      if (file_exists($p = sprintf('%s/%s.php', $path, $view))) return $p;
    }

    foreach ($this->paths as $path) {

      if (file_exists($p = sprintf('%s/views/%s.php', $path, $view))) return $p;
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

  public function index(): void {
    request::isPost() ?
      $this->postHandler() :
      $this->_index();
  }

  public function requiresAuthentication(): bool {

    return $this->requireAuthentication;
  }
}
