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

class request {

  protected static ?dto\request $_instance = null;

  protected function __construct() {

    $dto = new dto\request;

    $dto->uri = trim($_SERVER['REQUEST_URI'], '/');

    $uri = filter_var($dto->uri, FILTER_SANITIZE_URL);
    $uri = preg_replace('/\?(.*)$/', '', $uri);
    $segs = $uri ? explode('/', $uri) : [];

    if ($segs) $dto->controller = array_shift($segs);
    if ($segs) $dto->method = array_shift($segs);
    if ($segs) $dto->param1 = array_shift($segs);
    if ($segs) $dto->param2 = array_shift($segs);

    // $dto->get = $_SERVER
    $dto->post = $_POST;
    $dto->get = $_GET;
    $dto->params = array_merge($dto->post, $dto->get);

    $dto->isPost = (bool)($_SERVER['REQUEST_METHOD'] == 'POST');
    $dto->isGet = (bool)($_SERVER['REQUEST_METHOD'] == 'GET');

    self::$_instance = $dto;
  }

  protected static function __init(): void {

    if (!self::$_instance) new self();
  }

  public static function controller(): string {

    self::__init();
    return self::$_instance->controller;
  }

  public static function dto(): dto\request {

    self::__init();
    return self::$_instance;
  }

  public static function isGet(): bool {

    self::__init();
    return self::$_instance->isPost;
  }

  public static function isPost(): bool {

    self::__init();
    return self::$_instance->isPost;
  }

  public static function get(string $var): string {

    self::__init();
    return self::$_instance->get[$var] ?? '';
  }

  public static function param(string $var): string {

    self::__init();
    return self::$_instance->params[$var] ?? '';
  }

  public static function post(string $var): string {

    self::__init();
    return self::$_instance->post[$var] ?? '';
  }

  public static function method(): string {

    self::__init();
    return self::$_instance->method;
  }

  public static function param1(): string {

    self::__init();
    return self::$_instance->param1;
  }

  public static function param2(): string {

    self::__init();
    return self::$_instance->param2;
  }
}
