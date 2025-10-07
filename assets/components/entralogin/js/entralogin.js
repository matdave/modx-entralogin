var EntraLogin = function (config){
    config = config || {};
    EntraLogin.superclass.constructor.call(this, config);
};
Ext.extend(EntraLogin, Ext.Component, {
    page:{},
    window:{},
    grid:{},
    panel:{},
    combo:{},
    config: {},
    debug: []
});
Ext.reg('entralogin', EntraLogin);
entralogin = new EntraLogin();