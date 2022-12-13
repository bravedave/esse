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

use strings;  ?>

<ul class="list-unstyled" id="<?= $_uidMatrix = strings::rand() ?>"></ul>
<script>
  (_ => {

    const edit = function(e) {

      e.stopPropagation();

      let _me = $(this);
      let _dto = _me.data('dto');

      _.get.modal(_.url(`<?= $this->route ?>/edit/${_dto.id}`))
        .then(m => m.on('success', e => _me.trigger('refresh')));
    };

    const matrix = data => {
      let ul = $('#<?= $_uidMatrix ?>');

      ul.html('');
      $.each(data, (i, dto) => {

        let li = $(`<li style="cursor: pointer">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" ${dto.complete ? 'checked' : '' }>
              <label class="form-check-label">${dto.description}</label>
            </div>
          </li`)
          .data('dto', dto)
          .on('edit', edit)
          .on('refresh', refresh)
          .on('toggle', toggle)
          .appendTo(ul);

        li.find('input').on('change', function(e) {

          $(this).trigger('toggle');
        });

        li.find('label').on('click', function(e) {

          e.stopPropagation();
          e.preventDefault();

          $(this).trigger('edit');
        });

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

          let dto = d.data;
          _me.data('dto', dto);
          _me.find('label').html(dto.description);
          _me.find('input[type="checkbox"]').prop('checked', 1 == Number(dto.complete));
        } else {

          _.growl(d);
        }
      });
    };

    const toggle = function(e) {
      e.stopPropagation();

      let _me = $(this);
      let _dto = _me.data('dto');

      let checked = _me.find('input[type="checkbox"]').prop('checked');

      $.post(_.url('<?= $this->route ?>'), {
        action: checked ? 'todo-set-complete-undo' : 'todo-set-complete',
        id: _dto.id
      }).then(d => {

        _.growl(d);
        if ('ack' == d.response) {

          _me.trigger('refresh');
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