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
            try {
                $loginURL = $this->service->client->getAuthorizationUrl();
                if (!empty($loginURL)) {
                    $this->modx->sendRedirect($loginURL);
                }
            } catch (\Exception $e) {
                $this->modx->log(\xPDO::LOG_LEVEL_ERROR, "Failed to register login URL: " . $e->getMessage());
            }
            $this->modx->event->output(
                $this->modx->lexicon('entralogin.disable_regular_login')
            );
        }
    }
}