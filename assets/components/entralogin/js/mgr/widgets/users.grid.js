entralogin.grid.Users = function (config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            rowselect: {
                fn: function (sm, rowIndex, record) {
                    this.rememberRow(record);
                }, scope: this
            },
            rowdeselect: {
                fn: function (sm, rowIndex, record) {
                    this.forgotRow(record);
                }
                ,scope: this
            }
        }
    });
    Ext.applyIf(config,{
        url: entralogin.config.connector_url,
        id: 'entralogin-grid-users',
        baseParams: {
            action: 'MODX\\EntraLogin\\Processors\\Users\\GetList'
        }
        ,fields: ['id',
            'username',
            'active',
            'add_groups',
            'primary_group',
            'primary_group_name',
            'primary_group_role',
            'profile_blocked',
            'profile_comment',
            'profile_email',
            'profile_fullname',
            'profile_lastlogin',
            'entra_status',
            'entra_value'
        ]
        ,sm: this.sm
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: [
            this.sm,
            {
                header: _('id')
                ,dataIndex: 'id'
                ,width: 70
                ,hidden: true
                ,sortable: true
            },{
                header: _('entralogin.users.entra_status')
                ,dataIndex: 'entra_value'
                ,width: 100
                ,sortable: true
                ,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                    console.log(record);
                    return _('entralogin.users.entra_status_' + record.data.entra_status);
                }
            },{
                header: _('entralogin.users.username')
                ,dataIndex: 'username'
                ,width: 100
                ,sortable: true
                ,hidden: false
            },{
                header: _('active')
                ,dataIndex: 'active'
                ,width: 100
                ,sortable: true
                ,hidden: true
                ,renderer: this.rendYesNo
            },{
                header: _('user_block')
                ,dataIndex: 'profile_blocked'
                ,width: 100
                ,sortable: true
                ,hidden: true
                ,renderer: this.rendYesNo
            },{
                header: _('user_full_name')
                ,dataIndex: 'profile_fullname'
                ,width: 200
                ,sortable: true
                ,hidden: false
            },{
                header: _('email')
                ,dataIndex: 'profile_email'
                ,width: 200
                ,sortable: true
                ,hidden: false
            },{
                header: _('primary_group')
                ,dataIndex: 'primary_group_name'
                ,width: 200
                ,sortable: true
                ,hidden: false
                ,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                    return value + ' (' + record.data.primary_group_role + ')';
                }
            },{
                header: _('entralogin.users.additional_groups')
                ,dataIndex: 'add_groups'
                ,width: 200
                ,sortable: true
                ,hidden: false
                ,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                    var groupNames = []
                    Ext.each(value, function (group, index) {
                        groupNames.push(group.name + ' (' + group.role + ')');
                    });
                    return groupNames.join(', ');
                }
            },{
                header: _('role')
                ,dataIndex: 'primary_group_role'
                ,width: 200
                ,sortable: false
                ,hidden: true
            },{
                header: _('comment')
                ,dataIndex: 'profile_comment'
                ,width: 200
                ,sortable: false
                ,hidden: true
            },{
                header: _('user_prevlogin')
                ,dataIndex: 'profile_lastlogin'
                ,width: 200
                ,sortable: false
                ,hidden: false
            }
        ]
        ,tbar: this.getTbar(config)
    });
    entralogin.grid.Users.superclass.constructor.call(this,config);
}
Ext.extend(entralogin.grid.Users, MODx.grid.Grid, {
    selectedRecords: [],
    getMenu: function () {
        var m = [];
        if (this.menu.record.entra_value) {
            m.push({
                text: _('entralogin.users.clear_entra'),
                handler: this.clearEntra,
                single: true,
            });
        }
        return m;
    },

    clearEntra: function (btn, e) {
        var btnConfig = btn.initialConfig.options || btn.initialConfig;
        var grid = Ext.getCmp('entralogin-grid-users');
        var ids = btnConfig.single ? [this.menu.record.id] : grid.getSelectedAsList();
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'MODX\\EntraLogin\\Processors\\Users\\Clear',
                user: ids,
            },
            listeners: {
                success: {
                    fn: function () {
                        grid.refresh();
                    }, scope: this
                }
            }
        });
    },

    selectRows: function (ids) {
        Ext.each(ids, function (id) {
            if (this.selectedRecords.indexOf(id) === -1) {
                this.selectedRecords.push(id);
                this.enableTbarButtons();

                var indexOfId = this.store.indexOfId(id);
                if (indexOfId !== -1) {
                    this.selModel.selectRow(indexOfId, true);
                }
            }
        },this);
    },

    unselectRows: function (ids) {
        Ext.each(ids, function (id) {
            this.selectedRecords.remove(id);
            if (this.selectedRecords.length === 0) {
                this.disableTbarButtons();
            }

            var indexOfId = this.store.indexOfId(id);
            if (indexOfId !== -1) {
                this.selModel.deselectRow(indexOfId);
            }
        },this);
    },

    rememberRow: function (record) {
        if (this.selectedRecords.indexOf(record.id) === -1) {
            this.selectedRecords.push(record.id);
            this.enableTbarButtons();
        }
    },

    forgotRow: function (record) {
        this.selectedRecords.remove(record.id);
        if (this.selectedRecords.length === 0) {
            this.disableTbarButtons();
        }
    },

    disableTbarButtons: function () {
        Ext.getCmp('entralogin-all_changes-with-selected').disable();
    },

    enableTbarButtons: function () {
        Ext.getCmp('entralogin-all_changes-with-selected').enable();
    },

    getSelectedAsList: function () {
        return this.selectedRecords.join();
    },
    filterSearch: function (comp, search) {
        var s = this.getStore();
        s.baseParams[comp.filterName] = search;
        this.getBottomToolbar().changePage(1);
    },
    filterCombo: function (combo, record) {
        var s = this.getStore();
        s.baseParams[combo.filterName] = record.data[combo.valueField];
        this.getBottomToolbar().changePage(1);
    },
    exportFilters: function (comp, search) {
        var s = this.getStore();
        var filters = "export=true&HTTP_MODAUTH=" + MODx.siteId;
        Object.keys(s.baseParams).forEach((key) => {
            filters += "&" + key + "=" + s.baseParams[key];
        });
        window.location = this.config.url + "?" + filters;
    },
    getTbar: function (config) {
        var tbar = [];

        tbar.push([
            {
                text: _('entralogin.users.with-selected'),
                id: 'entralogin-all_changes-with-selected',
                disabled: true,
                menu: [{
                    text: _('entralogin.users.clear_entra'),
                    single: false,
                    config: config,
                    handler: this.clearEntra,
                }
                ]
            },{
                text: _("entralogin.users.export"),
                handler: this.exportFilters,
                scope: this,
            },'->',{
                xtype: 'entralogin-combo-use-2FA',
                name: '2fa',
                scope: this,
                filterName: "2fa",
                listeners: {
                    select: this.filterCombo,
                    scope: this
                }
            },{
                xtype: 'entralogin-combo-user-active',
                name: 'active',
                scope: this,
                filterName: "active",
                listeners: {
                    select: this.filterCombo,
                    scope: this
                }

            },{
                xtype: 'textfield',
                emptyText: _('search_ellipsis'),
                id: 'entralogin-filter-search',
                filterName: "search",
                listeners: {
                    change: this.filterSearch,
                    scope: this,
                    render: {
                        fn: function (cmp) {
                            new Ext.KeyMap(cmp.getEl(), {
                                key: Ext.EventObject.ENTER,
                                fn: this.blur,
                                scope: cmp,
                            });
                        },
                        scope: this,
                    },
                }
            }]);

        return tbar;
    },

});
Ext.reg('entralogin-grid-users', entralogin.grid.Users);
