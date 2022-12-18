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

class session {

	// THE only instance of the class
	protected static $instance;
	protected $__session = [];
	protected $open = false;
	protected $domain = null;

	protected function __construct() {

		$CookieParams = session_get_cookie_params();

		if (!is_null($this->domain))
			$CookieParams['domain'] = $this->domain;

		$CookieParams['secure'] = !(request::server_isLocal() || request::client_isLocal());

		if ((float)phpversion() < 7.3) {

			$CookieParams['path'] = '/; samesite=lax';

			session_set_cookie_params(
				$CookieParams['lifetime'],
				$CookieParams['path'],
				$CookieParams['domain'],
				$CookieParams['secure'],
				$CookieParams['httponly']

			);
		} else {

			$CookieParams['path'] = '/';
			$CookieParams['samesite'] = 'lax';

			session_set_cookie_params($CookieParams);
		}

		session_cache_expire(config::$SESSION_CACHE_EXPIRE);
		session_start();

		$this->__session = $_SESSION;

		session_write_close();
	}

	protected function __destroy(): void {

		if ($this->open) session_write_close();
	}

	protected function _edit(): void {

		if (!$this->open) {

			session_cache_expire(config::$SESSION_CACHE_EXPIRE);
			session_start();
			$this->open = true;
		}
	}

	protected function _get(string $var, string $default = ''): string {

		if (isset($this->__session[$var])) return $this->__session[$var];
		return $default;
	}

	protected function _close(): void {

		if (!isset(self::$instance)) self::$instance = new session;

		if ($this->open) {

			$this->__session = $_SESSION;	// re-read session
			$this->open = false;
			session_write_close();
		}
	}

	static function get(string $var, string $default = ''): string {

		if (!isset(self::$instance)) self::$instance = new session;
		return (self::$instance->_get($var, $default));
	}

	static function set(string $var, ?string $val = null): void {

		self::edit();
		if (is_null($val)) {

			if (isset($_SESSION[$var])) unset($_SESSION[$var]);
		} else {

			$_SESSION[$var] = $val;
		}
	}

	static function edit(): void {

		if (!isset(self::$instance)) self::$instance = new session;
		self::$instance->_edit();
	}

	static function close(): void {

		if (!isset(self::$instance)) self::$instance = new session;
		self::$instance->_close();
	}

	public static function destroy(string $msg = ''): void {
		self::close();

		session_start();
		session_destroy();

		if ($msg) logger::info(sprintf('<%s> %s', $msg, __METHOD__));
	}

	public function domain(?string $domain = null): string {

		$ret =  $this->domain;
		if (!is_null($domain)) $this->domain = $domain;
		return $ret;
	}
}
