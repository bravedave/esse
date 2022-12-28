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

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Logger as MonoLog;
use Monolog\Processor\IntrospectionProcessor;

use config;

class logger {
  protected static ?MonoLog $_monolog = null;
  protected static ?MonoLog $_monologEmail = null;

  protected static int $logLevel = MonoLog::INFO;

  protected static function monolog(bool $email = false): ?MonoLog {

    self::$logLevel = MonoLog::DEBUG; // turn on debugging level

    if ($email) {

      if ($mailer = sendmail::mailer()) {

        if (!self::$_monologEmail) {

          self::$_monologEmail = new MonoLog(config::$WEBNAME);

          $email = sendmail::email();
          $email->to(sendmail::address(config::$SUPPORT_EMAIL, config::$SUPPORT_NAME));

          $emailHandler = new SymfonyMailerHandler($mailer, $email);
          $emailHandler->pushProcessor(new IntrospectionProcessor(MonoLog::DEBUG, [
            'errsys'
          ]));
          self::$_monologEmail->pushHandler($emailHandler);
        }

        return self::$_monologEmail;
      } else {

        return self::monolog(); // plain ..
      }
    } else {

      if (!self::$_monolog) {

        // $path = sprintf('%s/application.log', config::dataPath());
        self::$_monolog = new MonoLog('esse');
        // self::$_monolog->pushHandler(new StreamHandler($path, MonoLog::WARNING));

        // $syslog = new SyslogHandler(config::$WEBNAME, LOG_USER, MonoLog::DEBUG, true, LOG_CONS);
        // $formatter = new LineFormatter("%channel%.%level_name%: %message% %context% %extra%");

        $syslog = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, self::$logLevel);
        $formatter = new LineFormatter("%channel%.%level_name%: %message% %context%");
        $syslog->setFormatter($formatter);
        $syslog->pushProcessor(new IntrospectionProcessor(self::$logLevel, [
          'errsys\\',
          'application\\'
        ]));
        self::$_monolog->pushHandler($syslog);

        // self::$_monolog->info('My logger is now ready');
      }

      return self::$_monolog;
    }
  }

  static public function debug(string $message): void {

    if (php_sapi_name() == 'cli') {

      echo $message . PHP_EOL;
    } else {

      self::monolog()->debug($message);
    }
  }

  static public function info(string $message): void {

    if (php_sapi_name() == 'cli') {

      echo $message . PHP_EOL;
    } else {

      self::monolog()->info($message);
    }
  }

  public static function sql(string $v): void {
    self::info(preg_replace(["@\r\n@", "@\n@", "@\t@", "@\s\s*@"], ' ', $v));
  }
}
