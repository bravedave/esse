<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace home;

use config;
use strings;

?>

<h1><?= config::$WEBNAME ?></h1>
<h4 class="text-muted mb-4 fst-italic"><?= config::$SLOGAN ?></h4>

<div class="row gw-2">
  <div class="col-md-2">

    <h2>what</h2>
  </div>

  <div class="col mb-2 pt-2">

    This is a PHP PSR-4 Framework - <a href="https://www.php-fig.org/psr/psr-4/">www.php-fig.org/psr/psr-4/</a>
  </div>
</div>

<div class="row gw-2">
  <div class="col-md-2">

    <h2>why</h2>
  </div>

  <div class="col mb-2 pt-2">

    <p>To craft PHP applications you need this style of Framework</p>
    <p>This is a <strong>M</strong>odel-<strong>V</strong>iew-<strong>C</strong>ontroller Application in PHP</p>

    <p>It:
    <ol>
      <li>Creates an entry point</li>
      <li>Loads php scripts as required</li>
      <li>Calls a Controller
        <ul>
          <li>Models data</li>
          <li>Displays a View</li>
        </ul>
      </li>
    </ol>
    </p>
  </div>

  <div class="col-md-4 mb-2">

    <img class="img-fluid" src="<?= strings::url($this->route . '/images/application.drawio.svg') ?>">
  </div>
</div>

<div class="row gw-2">
  <div class="col-md-2">

    <h2>how</h2>
  </div>

  <div class="col mb-2 pt-2">

    <ol>
      <li>Create a composer file
        <pre class="bg-light p-2">
{
  "license": "MIT",
  "minimum-stability": "dev",
  "autoload": {
    "psr-4": {
      "": "src/app"
    }
  },
  "require": {
    "bravedave/esse": "dev-main"
  },
  "repositories": {
    "bravedave-esse": {
      "type": "git",
      "url": "https://github.com/bravedave/esse"
    }
  }
}
</pre>
      </li>

      <li>update to install files
        <pre class="bg-light p-2">
composer u
</pre>
      </li>

      <li>install a sample application
        <pre class="bg-light p-2">
mkdir src
cp -r vendor/bravedave/esse/src/app src/app
cp -r vendor/bravedave/esse/www .
</pre>
      </li>

      <li>Run
        <pre class="bg-light p-2">
cd www
php -S localhost:8080 _mvp.php
</pre>
      </li>
    </ol>

    <p>the program will run, but there are no users or database</p>

    <ul>

      <li>a data folder was created in src/data
    </ul>

    <ol>

      <li>rename the esse-defaults-sample.json to esse-defaults.json <em>- activates SQLite as the database</em></li>
      <li>create a user in users with a password</li>
      <li>logoff</li>
      <li>authentication is now required</li>
    </ol>

    <p>dive into the app folder and build your app ! etc .. etc..</p>
  </div>
</div>