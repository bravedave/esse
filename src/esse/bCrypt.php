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

abstract class bCrypt {

  static function crypt($input) {

    return base64_encode(openssl_encrypt($input, config::$CIPHER, config::$CRYPT_KEY, 0, config::$CRYPT_IV));
  }

  static function decrypt($encrypted_text) {

    return openssl_decrypt(base64_decode($encrypted_text), config::$CIPHER, config::$CRYPT_KEY, 0, config::$CRYPT_IV);
  }
}
