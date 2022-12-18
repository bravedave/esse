<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="row" id="<?= $_row = strings::rand() ?>">
  <div class="offset-md-4 offset-lg-5 col">

    <form id="<?= $_form = strings::rand() ?>" autocomplete="off">

      <input type="hidden" name="action" value="-system-logon-">

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

      <div class="row">
        <div class="col mb-2">

          <button type="submit" class="btn btn-primary">logon</button>
        </div>
      </div>

      <script>
        (_ => {
          const form = $('#<?= $_form ?>')
          const row = $('#<?= $_row ?>')

          form
            .on('submit', function(e) {

              let _data = _.serialize(new FormData(this));
              $.post(_.url('<?= $this->route ?>'), _data)
                .then(d => {

                  if ('ack' == d.response) {

                    window.location.reload();
                  } else {

                    form.append($('<div class="alert alert-danger">failed</div>'));

                    _.growl(d);
                  }
                });

              // console.table( _data);

              return false;
            });

          form.find('input[name="u"]').focus();
        })(_esse_);
      </script>
    </form>
  </div>
</div>