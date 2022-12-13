/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {

  const generateRandomInt = (min, max) => Math.floor((Math.random() * (max + 1 - min)) + min);

  // https://dev.to/ovi/20-javascript-one-liners-that-will-help-you-code-like-a-pro-4ddc
  _.randomString = () => 'abcdefghijklmnopqrstuvwxyz'.charAt(generateRandomInt(0, 25)) + Math.random().toString(36).slice(2)
})(_esse_);