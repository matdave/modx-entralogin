<?php
namespace MODX\EntraLogin;

use MatDave\MODXPackage\Service as BaseService;
use Microsoft\Kiota\Abstractions\ApiException;
use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Microsoft\Kiota\Authentication\PhpLeagueAuthenticationProvider;
use Microsoft\Kiota\Http\GuzzleRequestAdapter;
use MODX\EntraLogin\Client\GraphApiClient;

class Service extends BaseService
{
    public const VERSION = '1.0.0';

    public $namespace = 'entralogin';

    public GraphApiClient $client;

    public function loadClient()
    {
        $clientId = $this->getOption('client_id');
        $clientSecret = $this->getOption('client_secret');
        $authorizationCode = $this->getOption('auth_code');
        if (empty($clientId) || empty($clientSecret) || empty($authorizationCode)) {
            $this->modx->log(\xPDO::LOG_LEVEL_ERROR, 'Entra Login: Client ID, Client Secret and Authorization Code are required.');
            return;
        }
        $tenantId = $this->getOption('tenant_id', 'common');
        $redirectUri = $this->options['assetsUrl'].'callback.php';
        $allowedHosts = $this->getOption('allowed_hosts', 'graph.microsoft.com');
        $allowedHosts = explode(',', $allowedHosts);
        if (empty($allowedHosts)) {
            $allowedHosts[] = 'graph.microsoft.com';
        }
        $scopes = ['User.Read'];
        try {
            $tokenRequestContext = new AuthorizationCodeContext(
                $tenantId,
                $clientId,
                $clientSecret,
                $authorizationCode,
                $redirectUri
            );

            $authProvider = new PhpLeagueAuthenticationProvider($tokenRequestContext, $scopes, $allowedHosts);
            $requestAdapter = new GuzzleRequestAdapter($authProvider);
            $this->client = new GraphApiClient($requestAdapter);
        } catch (ApiException $exception) {
            $this->modx->log(\xPDO::LOG_LEVEL_ERROR, $exception->getMessage());
        }
    }
}
