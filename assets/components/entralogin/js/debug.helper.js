Ext.onReady(function() {
    var div = Ext.get('modx-panel-holder');
    Ext.DomHelper.insertBefore(div, '<div class="container modx_error">' +
        '<div class="error_container">' +
         + JSON.stringify(entralogin.debug) +
        '</div>' +
    '</div>');
});