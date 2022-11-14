console.info(window.axeptio_plugin_configurations);

function sendAjaxTest(){
    jQuery.ajax({
        url: window.axeptio_plugin_configurations.ajax_url,
        method: 'post',
        data: {
            _ajax_nonce:window.axeptio_plugin_configurations.nonce,
            action: 'plugin_configurations'
        }
    })
}