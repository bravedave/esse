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
<div class="table-responsive">
  <table class="table table-sm" id="<?= $_uidMatrix = strings::rand() ?>">
    <thead class="small">
      <tr>
        <td>description</td>
        <td>complete</td>
      </tr>
    </thead>

    <tbody></tbody>

  </table>
</div>
<script>
  (_ => {

    const edit = function() {

      let _me = $(this);
      let _dto = _me.data('dto');

      _.get.modal(_.url(`<?= $this->route ?>/edit/${_dto.id}`))
        .then(m => m.on('success', e => _me.trigger('refresh')));
    };

    const matrix = data => {
      let table = $('#<?= $_uidMatrix ?>');
      let tbody = $('#<?= $_uidMatrix ?> > tbody');

      tbody.html('');
      $.each(data, (i, dto) => {
        $(`<tr style="cursor: pointer">
          <td class="js-description">${dto.description}</td>
          <td class="js-complete">${dto.complete}</td>
        </tr>`)
          .data('dto', dto)
          .on('click', function(e) {

            e.stopPropagation();
            e.preventDefault();

            $(this).trigger('edit');
          })
          .on('edit', edit)
          .on('refresh', refresh)
          .appendTo(tbody);
      });
    };

    const refresh = function(e) {
      e.stopPropagation();

      let _me = $(this);
      let _dto = _me.data('dto');

      $.post(_.url('<?= $this->route ?>'), {
        action: 'get-by-id',
        id: _dto.id
      }).then(d => {

        if ('ack' == d.response) {

          $('.js-description', _me).html(d.data.description);
          $('.js-complete', _me).html(d.data.complete);
        } else {

          _.growl(d);
        }
      });
    };

    $('#<?= $_uidMatrix ?>')
      .on('refresh', function(e) {
        $.post(_.url('<?= $this->route ?>'), {
            action: 'get-matrix'
          })
          .then(d => {

            if ('ack' == d.response) {

              matrix(d.data);
            } else {

              _.growl(d);
            }
          });
      });

    $(document).on('todo-add-new', e => $('#<?= $_uidMatrix ?>').trigger('refresh'));
    $(document).ready(() => $('#<?= $_uidMatrix ?>').trigger('refresh'));

  })(_esse_);
</script>