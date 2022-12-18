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

abstract class useragent {
	protected static $_useragent;

	static function html5Compliant() {
		// global $isAndroid, $isIPhone, $isIPad, $isIE, $isIE10, $isChrome, $isSafari, $isIPhoneWebApp, $isMobileDevice;
		// global $isChrome;
		if (self::isChrome()) return true;

		if (self::isIE()) {

			if (preg_match('/(?i)msie [1-8]/', $_SERVER['HTTP_USER_AGENT'])) return false;
			logger::info($_SERVER['HTTP_USER_AGENT']);
		}

		return true;
	}

	static function isAndroid() {

		return preg_match('/Android/', self::$_useragent);
	}

	static function isBlackberry() {

		return preg_match('/BlackBerry/', self::$_useragent);
	}

	static function isChrome() {

		return preg_match('/Chrome/', self::$_useragent);
	}

	static function isChromeOnIOS() {

		if (self::isIPhone()) return preg_match('/CriOS/', self::$_useragent);
		return false;
	}

	static function isEdge() {

		return (preg_match('/Edge/', self::$_useragent));
	}

	static function isFirefox() {

		return preg_match('/Gecko/', self::$_useragent);
	}

	static function isGoogleBot() {

		// HTTP_USER_AGENT => Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)
		if (preg_match('/Googlebot/', self::$_useragent))	return true;
		return false;
	}

	static function isIE() {

		return preg_match('/MSIE|Trident/', self::$_useragent);
	}

	static function isIPad() {

		return preg_match('/iPad/', self::$_useragent);
	}

	static function isIPhone() {

		return preg_match('/iPhone|iPod/', self::$_useragent);
	}

	static function isIPhoneWebApp() {

		return self::isSafari() && self::isIPhone();
	}

	static function isLegacyIE() {

		if (preg_match('/(?i)msie [5-8]/', self::$_useragent)) return true;
		return false;
	}

	static function isLegit() {

		if (self::isChrome()) {

			if (self::version() > 60) {
				// sys::logger( sprintf( 'Valid Chrome: %s : %s', self::version(), self::$_useragent));
				return true;
			}

			logger::info(sprintf('Invalid Chrome: %s : %s', self::version(), self::$_useragent));
			return false;
		} elseif (self::isFirefox()) {

			if (self::version() < 50) {

				/* this is probably nightly */
				$v = preg_replace('@^.*Firefox\/@', '', self::$_useragent);
				if ((float)$v > 59) return true;

				logger::info(sprintf('query Firefox: %s : %s : %s', (float)$v, $v, self::$_useragent));
			}

			if (self::version() > 50) {

				// sys::logger( sprintf( 'Valid Firefox: %s : %s', self::version(), self::$_useragent));
				return true;
			}

			logger::info(sprintf('Invalid Firefox: %s : %s', self::version(), self::$_useragent));
			return (false);
		}

		logger::info(self::$_useragent);

		if (self::isSafari()) return true;
		if (self::isEdge()) return true;
		if (self::isIE()) return true;
	}

	static function isMobileDevice() {

		return (self::isIPhone() || self::isBlackberry() || self::isAndroid() || self::isIPad());
	}

	static function isSafari() {

		return (preg_match('/Safari/', self::$_useragent));
	}

	static function os() {
		$os_array = [
			'/windows nt 10/i'     =>  'Windows 10',
			'/windows nt 6.3/i'     =>  'Windows 8.1',
			'/windows nt 6.2/i'     =>  'Windows 8',
			'/windows nt 6.1/i'     =>  'Windows 7',
			'/windows nt 6.0/i'     =>  'Windows Vista',
			'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     =>  'Windows XP',
			'/windows xp/i'         =>  'Windows XP',
			'/windows nt 5.0/i'     =>  'Windows 2000',
			'/windows me/i'         =>  'Windows ME',
			'/win98/i'              =>  'Windows 98',
			'/win95/i'              =>  'Windows 95',
			'/win16/i'              =>  'Windows 3.11',
			'/macintosh|mac os x/i' =>  'Mac OS X',
			'/mac_powerpc/i'        =>  'Mac OS 9',
			'/linux/i'              =>  'Linux',
			'/ubuntu/i'             =>  'Ubuntu',
			'/iphone/i'             =>  'iPhone',
			'/ipod/i'               =>  'iPod',
			'/ipad/i'               =>  'iPad',
			'/android/i'            =>  'Android',
			'/blackberry/i'         =>  'BlackBerry',
			'/webos/i'              =>  'Mobile'
		];

		foreach ($os_array as $regex => $value) {

			if (preg_match($regex, self::$_useragent)) return $value;
		}

		return sprintf('Unknown OS Platform (%s)', self::$_useragent);
	}

	static function version() {

		if (ini_get('browscap')) {

			/*
			 * get latest version of browscap.ini here: http://browscap.org/
			 */
			$browser = get_browser(null, false);
			// sys::dump( $browser);
			if (isset($browser->version)) return ($browser->version);
		}

		return 0;
	}

	static function init() {

		self::$_useragent = $_SERVER['HTTP_USER_AGENT']	?? '';
	}

	static function toString() {

		return self::$_useragent;
	}
}

useragent::init();
