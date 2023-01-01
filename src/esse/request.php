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

    $debug = false;
    // $debug = true;

    $dto = new dto\request;

    $dto->uri = trim($_SERVER['REQUEST_URI'], '/');

    $uri = filter_var($dto->uri, FILTER_SANITIZE_URL);
    $software = $_SERVER['SERVER_SOFTWARE'] ?? '';

    if (!preg_match('@^PHP@', $software)) {

      if (isset($_SERVER['SCRIPT_NAME'])) {

        if ($script = ltrim(dirname($_SERVER['SCRIPT_NAME']), '/')) {

          if ($debug) logger::debug(sprintf('<script %s> %s', $script, __METHOD__));
          $uri = ltrim(preg_replace('/^' . preg_quote($script, '/') . '/', '', $uri), '/');
        }
      }
    }

    if ($debug) logger::debug(sprintf('<uri %s> %s', $uri, __METHOD__));
    $uri = preg_replace('/\?(.*)$/', '', $uri);
    $segs = $uri ? explode('/', $uri) : [];

    if ($segs) $dto->controller = array_shift($segs);
    if ($segs) $dto->method = array_shift($segs);
    if ($segs) $dto->param1 = array_shift($segs);
    if ($segs) $dto->param2 = array_shift($segs);

    if ($debug) logger::debug(sprintf('<%s> <%s> %s', $dto->controller, $dto->method, __METHOD__));

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

  public static function getRemoteIP() {
    // https://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php

    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {

      if (array_key_exists($key, $_SERVER) === true) {

        foreach (explode(',', $_SERVER[$key]) as $ip) {

          $ip = trim($ip); // just to be safe
          if (self::server_isLocal()) {

            if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {

              // logger::info( sprintf('<%s> %s', $ip, __METHOD__));
              return $ip;
            }
          } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false) {

            return $ip;
          }
        }
      }
    }

    return '0.0.0.0';
  }

  public static function getServerIP(): string {

    return $_SERVER['SERVER_ADDR'] ?? '0.0.0.0';
  }

  public static function getSubNet(string $ip): string {

    if (false !== strpos((string)$ip, '.')) {

      $a = explode('.', $ip);
      if (4 == count($a)) {

        $subnet = sprintf('%d.%d.%d', $a[0], $a[1], $a[2]);
        // logger::info( sprintf('<%s> %s', $subnet, __METHOD__));
        return $subnet;
      }
    }

    return '';
  }

  public static function server_isLocal(): bool {

    return $_SERVER['SERVER_NAME'] ?? '' == 'localhost';
  }

  public static function client_isLocal() {
    if (self::server_isLocal()) return true;

    $thisIP = self::getServerIP();
    $remoteIP = self::getRemoteIP();

    $thisSubNet = self::getSubNet($thisIP);
    $remoteSubNet = self::getSubNet($remoteIP);

    // logger::info( sprintf( '%s/%s :: %s/%s', $thisIP, $thisSubNet, $remoteIP, $remoteSubNet));

    return ($thisSubNet == $remoteSubNet);
  }
}
