<?php

require_once dirname(__DIR__, 3) . '/config.core.php';
require_once MODX_CORE_PATH . "vendor/autoload.php";

$modx = new MODX\Revolution\modX();
$modx->initialize('mgr');

$entralogin = new \MODX\EntraLogin\Service($modx);
$callback = new \MODX\EntraLogin\Callback\Callback($entralogin);

try {
    $callback->handleCallback();
} catch (\Exception $e) {
    echo $e->getMessage();
}