<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace todo\dao;

use bravedave\esse\_dbinfo;
use bravedave\esse\logger;

class dbinfo extends _dbinfo {
	/*
	 * it is probably sufficient to copy this file into the
	 * <application>/app/dao folder
	 *
	 * from there store you structure files in
	 * <application>/dao/db folder
	 *
	*/
	protected function check(): void {

		parent::check();

		logger::info('checking ' . dirname(__FILE__) . '/db/*.php');

		if (glob(dirname(__FILE__) . '/db/*.php')) {
			foreach (glob(dirname(__FILE__) . '/db/*.php') as $f) {
				logger::info('checking => ' . $f);
				include_once $f;
			}
		}
	}
}
