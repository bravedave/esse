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

use strings;

?>

<ul class="nav flex-column">
  <li class="nav-item h6"><?= $this->title ?></li>

  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url('todo') ?>">todo</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url($this->route . '/goodbye') ?>">Goodbye</a>
  </li>
</ul>