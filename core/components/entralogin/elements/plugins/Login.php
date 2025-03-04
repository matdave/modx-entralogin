<?php

/** 
 *  EntraLogin
 *
 * @var $modx \modX
 * @var $scriptProperties array 
 */

use MatDave\MODXPackage\Elements\Event\Event;

if (empty($modx->version)) {
    $modx->getVersionData();
}

$version = (int) $modx->version['version'];

$scriptProperties = $scriptProperties ?? [];

if ($version > 2) {
    $elog = new MODX\EntraLogin\Service($modx, $scriptProperties);

    $className = "\\MODX\\EntraLogin\\Event\\{$modx->event->name}";
} else {    
    $corePath = $modx->getOption('entralogin.core_path', null, $modx->getOption('core_path') . 'components/entralogin/');
    $elog = $modx->getService('entralogin', 'EntraLogin', $corePath . 'model/entralogin/', $scriptProperties);

    $className = "\\MODX\\EntraLogin\\v2\\Event\\{$modx->event->name}";
}
if (class_exists($className)) {
    /** @var Event $event */
    $event = new $className($elog, $scriptProperties);
    $event->run();
} else {
    $modx->log(\xPDO::LOG_LEVEL_ERROR, "Class {$className} not found");
}
return;