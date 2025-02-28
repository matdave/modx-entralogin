Ext.onReady(function() {
    var div = Ext.get('modx-panel-profile-update');
    if (entralogin.config.loginUrl) {
        Ext.DomHelper.insertAfter(div, '<div class="x-panel container">'
            + `<a class="x-btn primary-button x-btn-text" href=${entralogin.config.loginUrl} >${_('entralogin.connect_entra')}</a>`
            + '</div>');
    }
    if (entralogin.config.entralogId) {
        Ext.DomHelper.insertAfter(div, '<div class="x-panel container">'
            + `<a class="x-btn primary-button x-btn-text" href=${entralogin.config.disconnectUrl} >${_('entralogin.disconnect_entra')}</a>`
            + '</div>');
    }
});
