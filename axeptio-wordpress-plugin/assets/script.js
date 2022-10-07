window.axeptioSettings = {
  clientId: sdk_script_vars.clientId,
  cookiesVersion: sdk_script_vars.version
};

(function (d, s) {
  var t = d.getElementsByTagName(s)[0],
    e = d.createElement(s);
  e.async = true;
  e.src = '//static.axept.io/sdk.js';
  t.parentNode.insertBefore(e, t);
})(document, 'script');
