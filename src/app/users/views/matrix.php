<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace users;

use strings;

?>

<div class="row my-2 gx-2" id="<?= $search = strings::rand() ?>">
  <div class="col">

    <input type="search" class="form-control js-user-matrix-search" autocomplete="filter table">
  </div>

  <div class="col-auto">
    <button type="button" class="btn btn-outline-secondary js-user-add"><i class="bi bi-plus"></i></button>
  </div>

</div>

<h1 class="d-none d-print-block"><?= $this->title ?></h1>
<div class="table-responsive">
  <table class="table table-sm" id="<?= $_table = strings::rand() ?>">
    <thead class="small">
      <tr>
        <td class="text-center js-line-number"></td>
        <td>name</td>
        <td>email</td>
        <td>mobile</td>
        <td>admin</td>
        <td>active</td>
      </tr>
    </thead>

    <tbody></tbody>

  </table>

</div>
<script>
  (_ => {
    const search = $('#<?= $search ?>');
    const table = $('#<?= $_table ?>');

    const edit = function(e) {

      e.stopPropagation();

      let _me = $(this);
      let _dto = _me.data('dto');

      _.get.modal(_.url(`<?= $this->route ?>/edit/${_dto.id}`))
        .then(m => m.on('success', e => _me.trigger('refresh')));
    };

    const matrix = data => {

      // console.table(data);
      let tbody = table.find('tbody');
      tbody.html('');

      $.each(data, (i, dto) => {

        $(`<tr class="pointer">
          <td class="text-center js-line-number small"></td>
          <td>${dto.name}</td>
          <td>${dto.email}</td>
          <td>${dto.mobile}</td>
          <td>${dto.admin}</td>
          <td>${dto.active}</td>
        </tr>`)
          .data('dto', dto)
          .on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();

            $(this).trigger('edit');

          })
          .on('edit', edit)
          .appendTo(tbody);
      });

      table.trigger('update-line-numbers');
    }

    search.find('.js-user-add').on('click', function(e) {
      e.stopPropagation();

      _.get.modal('<?= $this->route ?>/edit')
        .then(m => m.on('success', e => {
          e.stopPropagation();

          table.trigger('refresh');
        }));

    });

    table
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
      })
      .on('update-line-numbers', function(e) {
        let t = 0;
        $('> tbody > tr:not(.d-none) >td.js-line-number', this).each((i, e) => {
          $(e).data('line', i + 1).html(i + 1);
          t++;
        });
        $('> thead > tr > .js-line-number', this).data('count', t).html(t);
      });

    $(document).ready(() => table.trigger('refresh'));

  })(_esse_);
</script>