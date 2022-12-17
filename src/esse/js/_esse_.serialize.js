/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {

  _.serialize = data => {

    let obj = {};
    for (let [key, value] of data) {

      if (obj[key] !== undefined) {

        if (!Array.isArray(obj[key])) obj[key] = [obj[key]];
        obj[key].push(value);
      } else {

        obj[key] = value;
      }
    }
    return obj;
  };
})(_esse_);