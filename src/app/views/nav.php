<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse;

use strings;  ?>

<nav class="navbar navbar-expand-md navbar-dark bg-gradient-navbar">

  <div class="container-fluid">

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav me-auto mb-2 mb-md-0">

        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?= strings::url() ?>">Home</a>
        </li>
      </ul>

      <form class="d-flex flex-fill mx-md-2" role="search">
        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </form>
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <a class="navbar-brand" href="#"><?= $this->title ?? 'Navbar' ?></a>

  </div>
</nav>