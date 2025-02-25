<?php
use MODX\EntraLogin\Client\GraphApiClient;
use Microsoft\Kiota\Abstractions\ApiException;
use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Microsoft\Kiota\Authentication\PhpLeagueAuthenticationProvider;
use Microsoft\Kiota\Http\GuzzleRequestAdapter;

require_once dirname(__DIR__, 3) . '/config.core.php';
require_once MODX_CORE_PATH . "vendor/autoload.php";

$modx = new MODX\Revolution\modX();
$modx->initialize('web');

try {
    $clientId = $modx->getOption('entralogin.client_id', null, 'clientId');
    $clientSecret = $modx->getOption('entralogin.client_secret', null, 'clientSecret');
    $authorizationCode = 'authCode';

    $tenantId = 'common';
    $redirectUri = $modx->getOption('site_url').'/pkgs/entralogin/assets/components/entralogin/callback.php';

    // The auth provider will only authorize requests to
    // the allowed hosts, in this case Microsoft Graph
    $allowedHosts = ['graph.microsoft.com'];
    $scopes = ['User.Read'];

    $tokenRequestContext = new AuthorizationCodeContext(
        $tenantId,
        $clientId,
        $clientSecret,
        $authorizationCode,
        $redirectUri
    );

    $authProvider = new PhpLeagueAuthenticationProvider($tokenRequestContext, $scopes, $allowedHosts);
    $requestAdapter = new GuzzleRequestAdapter($authProvider);
    $client = new GraphApiClient($requestAdapter);

    $me = $client->me()->get()->wait();
    echo "Hello {$me->getDisplayName()}, your ID is {$me->getId()}";

} catch (ApiException $ex) {
    echo $ex->getMessage();
}
?>