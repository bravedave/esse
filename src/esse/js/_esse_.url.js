/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {

  _.url = path => {

    let url = new URL(window.location.href);
    url.pathname = !!_.url.root ? _.url.root + '/' : '' + !!path ? path : '';

    return url.href;
  }

  _.url.root = '';
})(_esse_);