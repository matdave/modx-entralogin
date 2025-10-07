entralogin.panel.Users = function (config) {
    config = config || {};
    Ext.applyIf(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>' + _('entralogin.users') + '</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        }, {
            xtype: 'entralogin-grid-users',
            cls: 'main-wrapper',
        }]
    });
    entralogin.panel.Users.superclass.constructor.call(this,config);
}
Ext.extend(entralogin.panel.Users, MODx.Panel);
Ext.reg('entralogin-panel-users', entralogin.panel.Users);
