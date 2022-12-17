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

use strings; ?>

<ul class="nav flex-column" id="<?= $_nav = strings::rand() ?>">
  <li class="nav-item h6"><?= $this->title ?></li>

  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url('todo') ?>">todo</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url('users') ?>">users</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url($this->route . '/goodbye') ?>">goodbye</a>
  </li>
  <li class="nav-item">
    <a class="nav-link js-logon" href="#">logon</a>
  </li>
</ul>
<script>
  (_ => {
    const nav = $('#<?= $_nav ?>');

    nav.find('.js-logon').on('click', function(e) {
      e.stopPropagation();
      e.preventDefault();

      _.get.modal(_.url('logon'));
      console.log('u');
    });
  })(_esse_);
</script>