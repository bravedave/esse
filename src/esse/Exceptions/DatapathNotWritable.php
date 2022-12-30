<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\Exceptions;

use Exception, Throwable, bravedave\esse\logger;

class DatapathNotWritable extends Exception {

  public function __construct($message = null, int $code = 0, Throwable $previous = null) {

    logger::info(sprintf('please create a writable data folder : %s', $message));

    // make sure everything is assigned properly
    parent::__construct($message, $code, $previous);
  }
}
