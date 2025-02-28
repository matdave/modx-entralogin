var EntraLogin = function (config){
    config = config || {};
    EntraLogin.superclass.constructor.call(this, config);
};
Ext.extend(EntraLogin, Ext.Component, {
    config: {},
});
Ext.reg('entralogin', EntraLogin);
entralogin = new EntraLogin();