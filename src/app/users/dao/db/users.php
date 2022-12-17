<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\esse\db;

$dbc = db::dbCheck('users');

$dbc->defineField('name', 'varchar', 100);
$dbc->defineField('email', 'varchar', 100);
$dbc->defineField('mobile', 'varchar');
$dbc->defineField('password', 'varchar');
$dbc->defineField('admin', 'tinyint');
$dbc->defineField('active', 'tinyint');
$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->check();
