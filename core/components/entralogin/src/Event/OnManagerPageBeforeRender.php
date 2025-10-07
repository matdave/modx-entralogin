<?php

namespace MODX\EntraLogin\Event;

use MatDave\MODXPackage\Elements\Event\Event;
use MODX\EntraLogin\Service;
use MODX\Revolution\modUser;
use MODX\Revolution\modUserSetting;
use xPDO\xPDO;

class OnManagerPageBeforeRender extends Event
{
    /**
     * @var Service
     */
    protected $service;
    public function run()
    {
        // System Wide
        $controller = $this->scriptProperties['controller'];
        $action = $controller->config['controller'] ?? null;
        $namespace = $controller->config['namespace'] ?? null;
        if ($namespace == 'entralogin') return;
        /** @var modUser $user */
        $user = $this->modx->user;
        if (!$user || $user->id === 0) {
            return;
        }
        $userSettings = $user->getSettings();
        $entralogId = $userSettings['entralog_id'] ?? null;
        $this->modx->controller->addJavascript($this->service->getOption('jsUrl') . 'entralogin.js');
        if ($this->service->getOption('disable_regular_login') && empty($entralogId)) {
            $this->modx->controller->addLexiconTopic('entralogin:default');
            $this->modx->controller->addLastJavascript($this->service->getOption('jsUrl') . 'warning.helper.js');
        }
        if ($action === 'security/profile') {
            if (isset($_GET['entralog']) && $_GET['entralog'] === 'disconnect') {
                $entralogSetting = $this->modx->getObject(modUserSetting::class, [
                    'key' => 'entralog_id',
                    'user' => $user->id,
                ]);
                if ($entralogSetting) {
                    $entralogSetting->remove();
                }
                $this->modx->sendRedirect($this->modx->getOption('manager_url') . '?a=security/profile');
            }
            $this->modx->controller->addLexiconTopic('entralogin:default');
            $loginUrl = null;
            $disconnectUrl = null;
            if (empty($entralogId)) {
                $this->service->loadClient();
                if (empty($this->service->client)) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'EntraLogin client not loaded');
                    return;
                }
                try {
                    $loginUrl = $this->service->client->getAuthorizationUrl();
                } catch (\Exception $e) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, $e->getMessage());
                }
            } else {
                $disconnectUrl = $this->modx->getOption('manager_url') . '?a=security/profile&entralog=disconnect';
            }
            $this->modx->controller->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                entralogin.config = ' . $this->modx->toJSON([
                    'entralogId' => $entralogId,
                    'loginUrl' => $loginUrl,
                    'disconnectUrl' => $disconnectUrl,
                ]) . ';
            });
            </script>');
            $this->modx->controller->addLastJavascript($this->service->getOption('jsUrl') . 'profile.helper.js');
        }
    }
}
