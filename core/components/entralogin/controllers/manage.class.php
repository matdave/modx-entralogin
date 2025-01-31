<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';

class EntraLoginManageManagerController extends EntraLoginBaseManagerController
{

    public function process(array $scriptProperties = []): void
    {
    }

    public function getPageTitle(): string
    {
        return $this->modx->lexicon('entralogin');
    }

    public function loadCustomCssJs(): void
    {
        $this->addLastJavascript($this->entralogin->getOption('jsUrl') . 'mgr/widgets/manage.panel.js');
        $this->addLastJavascript($this->entralogin->getOption('jsUrl') . 'mgr/sections/manage.js');

        $this->addHtml(
            '
            <script type="text/javascript">
                Ext.onReady(function() {
                    MODx.load({ xtype: "entralogin-page-manage"});
                });
            </script>
        '
        );
    }

    public function getTemplateFile(): string
    {
        return $this->entralogin->getOption('templatesPath') . 'manage.tpl';
    }

}
