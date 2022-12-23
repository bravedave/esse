<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\esse\logger;

class config extends \bravedave\esse\config {

  static function initialize(): void {

    parent::initialize();

    if (!self::checkDBconfigured()) {

      logger::info(sprintf('<%s> %s', 'DB not configured', __METHOD__));
      self::$AUTHENTICATION = false;
    }
  }
}
