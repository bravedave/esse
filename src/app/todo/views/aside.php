<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace todo;

use strings;

?>

<h6 class="mt-1">Todo</h6>

<ul class="nav flex-column">

  <li class="nav-item">
    <a class="nav-link" href="#" id="<?= $_uidAdd = strings::rand() ?>"><i class="bi bi-plus-circle"></i> new</a>
  </li>
</ul>
<script>
  (_ => {

    const addButton = $('#<?= $_uidAdd ?>');

    $(document).on('todo-add-new', () => {
      console.log('got that');
    })

    addButton.on('click', function(e) {
      e.stopPropagation();
      e.preventDefault();

      /**
       * note:
       * on success of adding new, tell the document there
       * was a new record, will be used by the matrix
       *  */

      _.get.modal(_.url('<?= $this->route ?>/edit'))
        .then(modal => {
          modal.on('success', (e, data) => $(document).trigger('todo-add-new'))
        });
    });

    $(document).ready(() => {});
  })(_esse_);
</script>