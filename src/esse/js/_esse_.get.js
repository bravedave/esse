/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {
  _.get = url => fetch(url)
    .then(response => response.text());

  _.get.modal = url => _.get(url)
    .then(modal => {
      let _modal = $(modal);

      if (_modal.hasClass('modal')) {

        _modal.appendTo('body');
        _modal.on('hidden.bs.modal', e => _modal.remove());
      } else {

        let w = $('<div></div>');

        w.append(_modal).appendTo('body');
        _modal = w.find('.modal');
        _modal.on('hidden.bs.modal', e => w.remove());
      }

      _modal.modal('show');
      return _modal;
    });

})(_esse_);