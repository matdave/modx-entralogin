<?php

namespace MODX\EntraLogin\v2\Event;

use MatDave\MODXPackage\Elements\Event\Event;
use MODX\EntraLogin\Service;

class OnManagerLoginFormRender extends Event
{
    /**
     * @var Service
     */
    protected $service;
    
    public function run()
    {
        $this->service->loadClient();
        if (empty($this->service->client)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'EntraLogin client not loaded');
            return;
        }
        $this->modx->controller->addLexiconTopic('entralogin:default');
        $css = '<link href="'. $this->service->options['cssUrl'] .'entralogin.css" rel="stylesheet"/>';
        $js = '<script src="'. $this->service->options['jsUrl'] .'login.helper.js"></script>';
        $invoke = '';
        if ($this->service->getOption('disable_regular_login')) {
            $invoke.= 'removeLoginOptions();';
        }
        $scripts = <<<HTML
<script>document.addEventListener("DOMContentLoaded", function() { {$invoke} });</script>
HTML;

        $message = '';
        if (isset($_GET['service']) && in_array($_GET['service'], ['success', 'fail'])) {
            $message = $this->modx->lexicon('entralogin.service_' . htmlentities($_GET['service']));
        }
        if (isset($_GET['signup'])) {
            $message = $this->modx->lexicon('entralogin.service_signup');
        }
        try {
            $loginURL = $this->service->client->getAuthorizationUrl();
        } catch (Exception $e)
        {
            $this->modx->log(\xPDO::LOG_LEVEL_ERROR, 'EntraLogin: Login URL not created '. $e->getMessage());
            return;
        }
        $this->modx->event->output("$js$css $message <a href=$loginURL class=\"c-button x-btn x-btn-small entra\" >".$this->modx->lexicon('entralogin.login_with_entra')."</a> $scripts");
    }
}