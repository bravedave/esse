<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class application extends bravedave\esse\application {

  public static function startDir(): string {

    return __DIR__;
  }

  public static function run(): void {

    new self(self::startDir());
  }
}
