<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">

  <input type="hidden" name="action" value="-system-logon-">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header <?= theme::modalHeader() ?>">

          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <div class="row">

            <div class="col mb-2">

              <input type="text" name="u" class="form-control" placeholder="username or email" autocomplete="username" required>
            </div>
          </div>

          <div class="row">
            <div class="col mb-2">

              <input type="password" name="p" class="form-control" placeholder="password" autocomplete="current-password" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">

          <button type="submit" class="btn btn-primary">logon</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    (_ => $('#<?= $_modal ?>').on('shown.bs.modal', () => {
      const form = $('#<?= $_form ?>')
      const modal = $('#<?= $_modal ?>')

      form
        .on('submit', function(e) {

          let _data = _.serialize(new FormData(this));
          $.post(_.url('<?= $this->route ?>'), _data)
            .then(d => {

              if ('ack' == d.response) {

                modal.trigger('success');
                window.location.reload();
              } else {

                form.find('.modal-body')
                  .append($('<div class="alert alert-danger">failed</div>'));

                _.growl(d);
              }
            });

          // console.table( _data);

          return false;
        });

      form.find('input[name="u"]').focus();
    }))(_esse_);
  </script>
</form>
