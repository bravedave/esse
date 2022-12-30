/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {
  _.table = {

    _line_numbers_: function (e) {
      let table = $(this);
      let t = 0;
      table.find('> tbody > tr:not(.d-none) >td.js-line-number').each((i, e) => {
        $(e).data('line', i + 1).html(i + 1);
        t++;
      });
      table.find('> thead > tr > .js-line-number').data('count', t).html(t);
    }
  }
})(_esse_);