<?php

abstract class EntraLoginBaseManagerController extends modExtraManagerController
{
    public string $version = '0.0.1';

    public $entralogin;

    public function checkPermissions()
    {
        return true;
    }
    public function initialize()
    {
        if (!$this->modx->version) {
            $this->modx->getVersionData();
        }
        $version = (int) $this->modx->version['version'];
        $corePath = $this->modx->getOption(
            'entralogin.core_path',
            null,
            $this->modx->getOption(
                'core_path',
                null,
                MODX_CORE_PATH
            ) . 'components/entralogin/'
        );
        if ($version > 2) {
            $this->entralogin = $this->modx->services->get('entralogin');
        } else {
            $this->entralogin = $this->modx->getService(
                'entralogin',
                'EntraLogin',
                $corePath . 'model/entralogin/',
                [
                    'core_path' => $corePath
                ]
            );
        }
        $this->addJavascript($this->entralogin->getOption('jsUrl') . 'entralogin.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            entralogin.config = ' . $this->modx->toJSON($this->entralogin->options) . ';
            entralogin.config.connector_url = "' . $this->entralogin->getOption('connectorUrl') . '";
            entralogin.config.modxVersion = ' . $version . ';
        });
        </script>');
    }

    public function getLanguageTopics()
    {
        return array('entralogin:default', 'user');
    }
}
