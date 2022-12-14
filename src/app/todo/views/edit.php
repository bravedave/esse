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

use strings, theme;

extract((array)$this->data);  ?>
<form id="<?= $_form = strings::rand() ?>" autocomplete="off">

  <input type="hidden" name="action" value="todo-save">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <!-- --[description]-- -->
          <div class="row">

            <div class="col-md-3 col-form-label">description</div>
            <div class="col mb-2">

              <input type="text" class="form-control" name="description" value="<?= $dto->description ?>" required>
            </div>
          </div>

          <!-- --[complete]-- -->
          <div class="row">

            <div class="offset-md-3 col mb-2">
              <div class="form-check">

                <input type="checkbox" class="form-check-input" name="complete" value="1" id="<?= $_uid = strings::rand() ?>" <?= $dto->complete ? 'checked' : '' ?>>
                <label class="form-check-label" for="<?= $_uid ?>">complete</label>
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
    (_ => {
      const form = $('#<?= $_form ?>');
      const modal = $('#<?= $_modal ?>');

      modal.on('shown.bs.modal', event => {

        form.on('submit', function(e) {

          try {

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

            // console.table(_data);

            return false;
          } catch (error) {

            console.error(error);
          }
        });

        form.find('input[name="description"]').focus();
      });
    })(_esse_);
  </script>
</form>