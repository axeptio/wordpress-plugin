jQuery(document).ready(function () {
  loadVersions(sdk_script_vars.clientId);
});

function loadVersionsOnChange() {
  document.getElementById('xpwp_version').innerHTML = '';
  var clientId = document.getElementById('xpwp_client_id').text;
  loadVersions(clientId);
}

function loadVersions(clientId) {
  var select = document.getElementById('xpwp_version');
  jQuery.ajax({
    type: 'get',
    dataType: 'json',
    url: 'https://client.axept.io/' + clientId + '.json',
    success: function (response) {
      for (let i = 0; i < response.cookies.length; i++) {
        let title = response.cookies[i].title;
        if (title != sdk_script_vars.version) {
          option = document.createElement('option');
          option.value = option.text = title;
          select.add(option);
        }
      }
    }
  });
}
