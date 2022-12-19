<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace hello;

use config, currentUser, strings; ?>

<ul class="nav flex-column" id="<?= $_nav = strings::rand() ?>">
  <li class="nav-item h6"><?= $this->title ?></li>

  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url('todo') ?>">todo</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url('users') ?>">users</a>
  </li>
  <?php if (config::$AUTHENTICATION && currentUser::isValid()) { ?>
    <li class="nav-item">
      <a class="nav-link" href="<?= strings::url('logon/logoff') ?>">logoff</a>
    </li>
  <?php } ?>
</ul>