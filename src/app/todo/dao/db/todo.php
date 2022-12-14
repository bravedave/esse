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

$dbc = db::dbCheck('todo');

// note id, autoincrement primary key is added to all tables - no need to specify

$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->defineField('description', 'varchar');
$dbc->defineField('complete', 'tinyint');

$dbc->check();  // actually do the work, check that table and fields exist