<?php

require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';

$tstart= microtime(true);

/* include modX class - return error on failure */
if (!include_once(MODX_CORE_PATH . 'model/modx/modx.class.php')) {
    header("Content-Type: application/json; charset=UTF-8");
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo json_encode(array(
        'success' => false,
        'code' => 404,
    ));
    die();
}

/* load modX instance */
$modx= new modX();
if (!is_object($modx) || !($modx instanceof modX)) {
    ob_get_level() && @ob_end_flush();
    $errorMessage = '<a href="setup/">MODX not installed. Install now?</a>';
    @include(MODX_CORE_PATH . 'error/unavailable.include.php');
    header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
    exit();
}
$modx->startTime= $tstart;

$modx->initialize('mgr');

$corePath = $modx->getOption(
    'entralogin.core_path',
    null,
    $modx->getOption('core_path') . 'components/entralogin/'
);
$entralogin = $modx->getService(
    'entralogin',
    'EntraLogin', $corePath . 'model/entralogin/',
    [
        'core_path' => $corePath
    ]
);


$callback = new \MODX\EntraLogin\v2\Callback\Callback($entralogin);

try {
    $callback->handleCallback();
} catch (\Exception $e) {
    echo $e->getMessage();
}