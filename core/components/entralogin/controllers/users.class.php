<?php

require_once dirname(__FILE__) . '/index.class.php';

class EntraLoginUsersManagerController extends EntraLoginBaseManagerController
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('entralogin_manage_auth');
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('entralogin.users');
    }

    public function process(array $scriptProperties = []) {}

    public function loadCustomCssJs()
    {
        $this->addCss($this->entralogin->getOption('cssUrl') . 'entralogin.css');
        $this->addLastJavascript($this->entralogin->getOption('jsUrl') . 'mgr/helpers/combo.js?v=' . $this->version);
        $this->addLastJavascript($this->entralogin->getOption('jsUrl') . 'mgr/widgets/users.grid.js?v=' . $this->version);
        $this->addLastJavascript($this->entralogin->getOption('jsUrl') . 'mgr/widgets/users.panel.js?v=' . $this->version);
        $this->addLastJavascript($this->entralogin->getOption('jsUrl') . 'mgr/sections/users.js?v=' . $this->version);
        $this->addHtml("<script>
        Ext.onReady(function() {
            MODx.load({ xtype: 'entralogin-page-users'});
        });
        </script>");
    }
    public function getTemplateFile()
    {
        return $this->entralogin->getOption('templatesPath') . 'users.tpl';
    }
}
