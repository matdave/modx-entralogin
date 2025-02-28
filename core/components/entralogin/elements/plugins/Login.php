<?php

use MatDave\MODXPackage\Elements\Event\Event;
use xPDO\xPDO;

$scriptProperties = $scriptProperties ?? [];

$elog = new MODX\EntraLogin\Service($modx, $scriptProperties);

$className = "\\MODX\\EntraLogin\\Event\\{$modx->event->name}";
if (class_exists($className)) {
    /** @var Event $event */
    $event = new $className($elog, $scriptProperties);
    $event->run();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, "Class {$className} not found");
}
return;