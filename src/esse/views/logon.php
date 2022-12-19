<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>
<style>
  html,
  body {
    height: 100%;
  }

  body {
    display: flex;
    align-items: center;
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  .form-signin {
    max-width: 330px;
    padding: 15px;
  }

  .form-signin .form-floating:focus-within {
    z-index: 2;
  }

  .form-signin input[type="email"] {
    margin-bottom: -1px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
  }

  .form-signin input[type="password"] {
    margin-bottom: 10px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
  }
</style>

<div class="form-signin w-100 m-auto">

  <form id="<?= $_form = strings::rand() ?>" autocomplete="off">

    <input type="hidden" name="action" value="-system-logon-">

    <div class="form-floating">

      <input type="email" name="u" class="form-control" id="<?= $_uid = strings::rand() ?>" placeholder="username or email" autocomplete="username" required>
      <label for="<?= $_uid ?>">email address</label>
    </div>

    <div class="form-floating">

      <input type="password" name="p" class="form-control" id="<?= $_uid = strings::rand() ?>" placeholder="password" autocomplete="current-password" required>
      <label for="<?= $_uid ?>">password</label>
    </div>

    <button type="submit" class="w-100 btn btn-lg btn-primary">logon</button>

    <script>
      (_ => {
        const form = $('#<?= $_form ?>')

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
      })(_esse_);
    </script>
  </form>
</div>