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
    url.pathname = !!path ? path : '';

    return url.href;
  }
})(_esse_);