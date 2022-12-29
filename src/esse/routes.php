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

abstract class routes {

  static protected function _route_map_path(): string {

    return config::dataPath() . '/esse-controller-map.json';
  }

  static protected function _route_map(): object {

    $defaults = [];

    $map = self::_route_map_path();
    if (\file_exists($map)) {

      return (object)array_merge(
        $defaults,
        (array)\json_decode(\file_get_contents($map))
      );
    }

    return (object)$defaults;
  }

  /**
   * set routes for controller
   * leave the second parameter blank to clear the setting
   *
   * @return void
   */
  static public function register(string $path, $register = false): void {

    $map = self::_route_map();
    if (!isset($map->{$path}) || $register != $map->{$path}) {

      if ($register) {

        $map->{$path} = $register;
      } else {

        unset($map->{$path});
      }

      \file_put_contents(self::_route_map_path(), \json_encode($map, JSON_PRETTY_PRINT));
    }
  }

  static public function map(string $path): string {

    $map = self::_route_map();
    return (isset($map->{$path}) ? $map->{$path} : '');
  }
}
