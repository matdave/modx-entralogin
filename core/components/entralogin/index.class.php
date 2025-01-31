<?php
abstract class EntraLoginBaseManagerController extends modExtraManagerController {
    /** @var \EntraLogin\EntraLogin $entralogin */
    public $entralogin;

    public function initialize(): void
    {
        $this->entralogin = $this->modx->services->get('entralogin');

        $this->addCss($this->entralogin->getOption('cssUrl') . 'mgr.css');
        $this->addJavascript($this->entralogin->getOption('jsUrl') . 'mgr/entralogin.js');

        $this->addHtml('
            <script type="text/javascript">
                Ext.onReady(function() {
                    entralogin.config = '.$this->modx->toJSON($this->entralogin->config).';
                });
            </script>
        ');

        parent::initialize();
    }

    public function getLanguageTopics(): array
    {
        return array('entralogin:default');
    }

    public function checkPermissions(): bool
    {
        return true;
    }
}
