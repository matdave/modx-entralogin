<?php

namespace MODX\EntraLogin\Event;

use MatDave\MODXPackage\Elements\Event\Event;
use MODX\EntraLogin\Service;

class OnBeforeManagerLogin extends Event
{
    /**
     * @var Service
     */
    protected $service;
    public function run()
    {
        $this->service->loadClient();
        if (empty($this->service->client)) {
            return;
        }
        if ($this->service->getOption('disable_regular_login')) {
            $this->modx->event->output(
                $this->modx->lexicon('entralogin.disable_regular_login')
            );
        }
    }
}