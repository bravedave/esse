<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://symfony.com/doc/current/mailer.html
*/

namespace bravedave\esse;

use config;
use RuntimeException;
use Symfony\Component\Mime\{
  Address,
  Email
};
use Symfony\Component\Mailer\{
  Transport,
  Mailer
};

abstract class sendmail {
  static $debug = false;

  // static protected function print_xml_error($error) : string {
  //   $return  = $error->message . "\n";
  //   $return .= str_repeat('-', $error->column) . "^\n";

  //   switch ($error->level) {
  //     case LIBXML_ERR_WARNING:
  //       $return .= "Warning $error->code: ";
  //       break;
  //     case LIBXML_ERR_ERROR:
  //       $return .= "Error $error->code: ";
  //       break;
  //     case LIBXML_ERR_FATAL:
  //       $return .= "Fatal Error $error->code: ";
  //       break;
  //   }

  //   $return .= trim($error->message) .
  //     "\n  Line: $error->line" .
  //     "\n  Column: $error->column";

  //   if ($error->file) {
  //     $return .= "\n  File: $error->file";
  //   }

  //   return "$return\n\n--------------------------------------------\n\n";
  // }

  static function address(string $email, string $name): address {

    return new Address($email, $name);
  }

  static function email(array $params = []): Email {

    $options = array_merge([
      'from' => self::address(config::$SUPPORT_EMAIL, config::$SUPPORT_NAME)
    ], $params);

    $email = new Email;

    $email->getHeaders()
      ->addTextHeader('X-Mailer', config::$MAILER);

    $email->from($options['from']);

    return $email;
  }

  public static function image2cid(string $msg, Email $mail): string {
    /**
     * The message may have embedded images,
     * these need to be converted to cid: type inline attachments
     *
     * Inline images are:
     * Pros
     *  Much simpler to achieve
     *  Much faster to do
     *  Requires much less deep dive into MIME and application code
     *
     * Cons
     *  Can really increase size of emails especially if you use more than one image
     *  Most likely blocked by default in many webmail services
     *  Blocked completely in Outlook
     **/

    //~ self::$debug = true;

    $types = [
      'image/jpeg' => 'jpg',
      'image/png'  => 'png',
      'image/gif'  => 'gif',
      'image/svg+xml' => 'svg',
      'image/webp' => 'webp'
    ];

    $msg = preg_replace('@\sid=@i', ' x-id=', $msg);  // to eliminate errors from duplicate id's

    $matches = [];
    $i = 0;
    if (preg_match_all('/src="data:image\/[^"]*?"/i', $msg, $matches)) {

      if (self::$debug) logger::debug(sprintf('%d matches found : %s', count($matches[0]), __METHOD__));

      foreach ($matches[0] as $match) {

        $src = trim(substr($match, 5), '" ');

        // Deconstruct it, get all the parts
        $semicolon_place = strpos($src, ';');
        $comma_place = strpos($src, ',');
        $type = trim(substr($src, 5, $semicolon_place - 5));

        if ($type && isset($types[$type])) {

          if (self::$debug) logger::debug(sprintf('%s => %s : %s', $type, substr($src, 0, $comma_place), __METHOD__));

          $base64_data = substr($src, $comma_place + 1);
          $data = base64_decode($base64_data);

          /**
           * the $i counter makes it unique if there are two images the same ....
           * maybe they could be re-used ?, if the md5 was the same would the images be the same ?
           */
          $mail->embed($data, $md5 = ($i++) . '_' . md5($data), $type);
          $msg = str_replace($src, 'cid:' . $md5, $msg);

          if (self::$debug) logger::debug(sprintf('src : %s : %s', $md5, __METHOD__));
        } else {

          logger::debug($error = sprintf('invalid type : %s( %d) : %s', $type, strlen($match), __METHOD__));
          throw new RuntimeException($error);
        }
      }
    }
    return $msg;
  }

  static function mailer(): ?Mailer {

    if (config::$MAILDSN) {

      $transport = Transport::fromDsn(\config::$MAILDSN);
      return new Mailer($transport);
    } else {

      logger::debug(sprintf('<%s> %s', 'please configure your config::$MAILDSN', __METHOD__));
    }

    return null;
  }

  static function send(Email $email): void {

    if ($mailer = self::mailer()) {

      $mailer->send($email);
    }
  }
}
