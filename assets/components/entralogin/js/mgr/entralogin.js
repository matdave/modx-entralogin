var EntraLogin = function (config) {
    config = config || {};
    EntraLogin.superclass.constructor.call(this, config);
};
Ext.extend(EntraLogin, Ext.Component, {

    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},

});
Ext.reg('entralogin', EntraLogin);
entralogin = new EntraLogin();
