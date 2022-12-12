<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\controller;

use bravedave\esse;
use bravedave\esse\jslib;
use bravedave\esse\logger;
use config;

/**
 * a default home controller
 */
class assets extends esse\controller {

  public function js($lib = 'esse.js'): void {

    if ('esse.js' == $lib) {

      jslib::viewjs([
        'debug' => false,
        'libName' => 'esse',
        'jsFiles' => config::libfiles,
        'libFile' => config::tempdir() . '_esse_.js'
      ]);
    }
  }
}
