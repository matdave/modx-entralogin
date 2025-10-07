entralogin.page.Users = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'entralogin-panel-users',
            renderTo: 'entralogin-panel-users-div'
        }]
    });
    entralogin.page.Users.superclass.constructor.call(this,config);
}
Ext.extend(entralogin.page.Users,MODx.Component);
Ext.reg('entralogin-page-users',entralogin.page.Users);
