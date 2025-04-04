<?php

namespace MODX\EntraLogin\Event;

use MatDave\MODXPackage\Elements\Event\Event;
use MODX\EntraLogin\Service;
use MODX\Revolution\modUser;

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

        if ($this->service->getOption('enforce_entra_login')) {
            $username = $this->scriptProperties['username'];
            if (!empty($username)) {
                $user = $this->modx->getObject(modUser::class, ['username' => $username]);
                if (!empty($user)) {
                    $entraSetting = $user->getOne('UserSettings', ['key' => 'entralog_id']);
                    if (!empty($entraSetting)) {
                        try {
                            $loginURL = $this->service->client->getAuthorizationUrl();
                            if (!empty($loginURL)) {
                                $this->modx->sendRedirect($loginURL);
                            }
                        } catch (\Exception $e) {
                            $this->modx->log(\xPDO::LOG_LEVEL_ERROR, "Failed to register login URL: " . $e->getMessage());
                        }
                        $this->modx->event->output(
                            $this->modx->lexicon('entralogin.enforce_entra_login')
                        );
                    }
                }
            }
        }
    }
}