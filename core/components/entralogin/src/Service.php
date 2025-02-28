<?php
namespace MODX\EntraLogin;

use Exception;
use MatDave\MODXPackage\Service as BaseService;
use MODX\EntraLogin\Services\Entra;

class Service extends BaseService
{
    public const VERSION = '1.0.0';

    public $namespace = 'entralogin';

    public Entra $client;

    public function loadClient()
    {
        $clientId = $this->getOption('client_id');
        $clientSecret = $this->getOption('client_secret');
        if (empty($clientId) || empty($clientSecret)) {
            $this->modx->log(\xPDO::LOG_LEVEL_ERROR, 'Entra Login: Client ID and Client Secret are required.');
            return;
        }
        $tenantId = $this->getOption('tenant_id', [], 'common');
        $redirectUri = rtrim($this->modx->getOption('site_url'), '/') .
            '/' .
            ltrim($this->options['assetsUrl'].'callback.php', '/');
        $graphHost = $this->getOption('graph_host', [], 'graph.microsoft.com');
        $authHost = $this->getOption('auth_host', [], 'login.microsoftonline.com');
        $scopes = [
            'user.read',
            'openid',
            'profile',
            'offline_access',
        ];

        try {
            $this->client = new Entra(
                $this,
                [
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'graphApiEndpoint' => 'https://' . $graphHost . '/v1.0',
                    'oauthUrl' => 'https://' . $authHost . '/',
                    'redirectUri' => $redirectUri,
                    'scope' => implode(' ', $scopes),
                    'tenantId' => $tenantId,
                ]);
        } catch (Exception $e) {
            $this->modx->log(\xPDO::LOG_LEVEL_ERROR, 'Entra Login: '.$e->getMessage());
        }
    }
}
