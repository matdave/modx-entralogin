entralogin.page.Manage = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [
            {
                xtype: 'entralogin-panel-manage',
                renderTo: 'entralogin-panel-manage-div'
            }
        ]
    });
    entralogin.page.Manage.superclass.constructor.call(this, config);
};
Ext.extend(entralogin.page.Manage, MODx.Component);
Ext.reg('entralogin-page-manage', entralogin.page.Manage);
