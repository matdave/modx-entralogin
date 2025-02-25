<?php

namespace MODX\EntraLogin\Callback;

use MODX\EntraLogin\Service;
use MODX\Revolution\modX;

class Callback
{
    protected Service $service;

    /** @var modX */
    public $modx = null;

    public function __construct($service)
    {
        $this->service = $service;
        $this->modx = $service->modx;
        $this->service->loadClient();
    }

    public function handleCallback(): void
    {
        if (empty($this->service->client)) {
            $data = file_get_contents('php://input');
            print_r($data);
            die();
        }
    }

    private function sendManager($success = false, $params = []): void
    {
        $extParams = '';
        foreach ($params as $key => $value) {
            $extParams .= "&$key=$value";
        }
        $this->modx->sendRedirect($this->modx->getOption('manager_url'). '?entralogin=' . ($success ? 'success' : 'fail') . $extParams);
    }

}