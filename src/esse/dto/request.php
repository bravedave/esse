<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\dto;

/**
 * the structure of a request
 */
class request {

  public string $controller = 'home';

  public string $method = 'index';

  public string $uri = '';

  public string $param1 = '';

  public string $param2 = '';

  public array $get = [];

  public array $post = [];

  public bool $isPost = false;

  public bool $isGet = false;

  public array $params = [];
}
