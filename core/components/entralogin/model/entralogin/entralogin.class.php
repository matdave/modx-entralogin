<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use MODX\EntraLogin\Service;

class EntraLogin extends Service
{

    public $callbackFile = 'callback.v2.php';
    public function __construct(&$modx, array $options = [])
    {
        $corePath = $modx->getOption('entralogin.core_path', $options, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/entralogin/');
        $assetsUrl = $modx->getOption('entralogin.assets_url', $options, $modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/entralogin/');

        /* loads some default paths for easier management */
        $options = array_merge([
            'modelPath' => $corePath . 'model/',
            'connectorUrl' => $assetsUrl . 'connector.php',
        ], $options);
        parent::__construct($modx, $options);
    }
    public function addPackage()
    {
        $this->modx->addPackage('entralogin', $this->getOption('modelPath'));
    }
}