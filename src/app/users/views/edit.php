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

use strings, theme;

extract((array)$this->data);  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">

  <input type="hidden" name="action" value="users-save">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <!-- name -->
          <div class="row gx-2">

            <div class="col-md-3 col-form-label">name</div>
            <div class="col mb-2">

              <input type="text" class="form-control" name="name" value="<?= $dto->name ?>">
            </div>
          </div>

          <!-- email -->
          <div class="row gx-2">

            <div class="col-md-3 col-form-label">email</div>
            <div class="col mb-2">

              <input type="email" class="form-control" name="email" value="<?= $dto->email ?>">
            </div>
          </div>

          <!-- mobile -->
          <div class="row gx-2">
            <div class="col-md-3 col-form-label">mobile</div>

            <div class="col mb-2">

              <input type="tel" class="form-control" name="mobile" value="<?= $dto->mobile ?>">
            </div>
          </div>

          <!-- password -->
          <div class="row gx-2">
            <div class="col-md-3 col-form-label">password</div>

            <div class="col mb-2">

              <input type="password" class="form-control" name="password" placeholder="only required to change">
            </div>
          </div>

          <!-- admin -->
          <div class="row gx-2">

            <div class="offset-md-3 col">

              <div class="form-check">

                <input type="checkbox" class="form-check-input" name="admin" value="1" id="<?= $_uid = strings::rand() ?>" <?= $dto->admin ? 'checked' : '' ?>>
                <label class="form-check-label" for="<?= $_uid ?>">admin</label>
              </div>
            </div>
          </div>

          <!-- active -->
          <div class="row gx-2">

            <div class="offset-md-3 col">

              <div class="form-check">

                <input type="checkbox" class="form-check-input" name="active" value="1" id="<?= $_uid = strings::rand() ?>" <?= $dto->active ? 'checked' : '' ?>>
                <label class="form-check-label" for="<?= $_uid ?>">active</label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    (_ => $('#<?= $_modal ?>').on('shown.bs.modal', () => {
      const form = $('#<?= $_form ?>')
      const modal = $('#<?= $_modal ?>')

      form
      $('#<?= $_form ?>')
        .on('submit', function(e) {
          let _data = _.serialize(new FormData(this));
          $.post(_.url('<?= $this->route ?>'), _data)
            .then(d => {

              if ('ack' == d.response) {

                modal
                  .trigger('success')
                  .modal('hide');
              } else {

                _.growl(d);
              }
            });

          // console.table( _data);

          return false;
        });
    }))(_esse_);
  </script>
</form>