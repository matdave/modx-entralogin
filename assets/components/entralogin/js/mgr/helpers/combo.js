entralogin.combo.UserActive = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ["l", "v"],
            data: [
                ["All", ""],
                ["Active", '1'],
                ["Inactive", '0'],
            ],
        }),
        displayField: 'l',
        valueField: 'v',
        emptyText: _('entralogin.user.activefilter.empty'),
        mode: "local",
        triggerAction: "all",
        editable: false,
        selectOnFocus: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true,
    });
    entralogin.combo.UserActive.superclass.constructor.call(this, config);
}
Ext.extend(entralogin.combo.UserActive, MODx.combo.ComboBox);
Ext.reg('entralogin-combo-user-active', entralogin.combo.UserActive);

entralogin.combo.Use2fa = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ["l", "v"],
            data: [
                ["All", ""],
                ["Enabled", '1'],
                ["Disabled", '0'],
            ],
        }),
        displayField: 'l',
        valueField: 'v',
        emptyText: _('entralogin.users.entra_value'),
        mode: "local",
        triggerAction: "all",
        editable: false,
        selectOnFocus: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true,
    });
    entralogin.combo.Use2fa.superclass.constructor.call(this, config);
}
Ext.extend(entralogin.combo.Use2fa, MODx.combo.ComboBox);
Ext.reg('entralogin-combo-use-2FA', entralogin.combo.Use2fa);
