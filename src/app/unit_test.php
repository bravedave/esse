<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\esse\sendmail;

class unit_test extends application {

  protected bool $service = true;

  protected static function _sendmail(): void {

    $address = sendmail::address(config::$SUPPORT_EMAIL, config::$SUPPORT_NAME);

    $email = sendmail::email()
      ->to($address)
      //->cc('cc@example.com')
      //->bcc('bcc@example.com')
      //->replyTo('fabien@example.com')
      //->priority(Email::PRIORITY_HIGH)
      ->subject('Sent using Symfony Mailer!')
      ->text('Sending emails is fun again!')
      ->html('<h1>Sending emails is fun again!</h1>');

    sendmail::send($email);
  }

  public static function sendmail(): void {

    $app = new self(self::startDir());
    $app->_sendmail();
  }
}
