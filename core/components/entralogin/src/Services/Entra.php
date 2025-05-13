<?php

namespace MODX\EntraLogin\Services;

use Exception;
use Firebase\JWT\CachedKeySet;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use MatDave\MODXPackage\Traits\Curl;
use MODX\EntraLogin\Service;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheExtensionNotInstalledException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;

class Entra
{
    use Curl;

    private Service $service;
    private array $option = [
        'clientId' => '',
        'clientSecret' => '',
        'scope' => '',
        'redirectUri' => '',
        'tenantId' => 'common',
        'oauthUrl' => 'https://login.microsoftonline.com/',
        'oauthAuthorizeEndpoint' => '/oauth2/v2.0/authorize',
        'oauthTokenEndpoint' => '/oauth2/v2.0/token',
        'userAgent' => 'MODX Entra Login',
        'graphApiEndpoint' => 'https://graph.microsoft.com/v1.0'
    ];
    private $tag = [];
    public string $api;

    private array $accessToken = [
        'token' => '',
        'created' => 0,
        'expires_in' => 0,
        'refresh_token' => ''
    ];

    /**
     * @throws Exception
     */
    public function __construct(&$service, array $options = [])
    {
        $this->service = $service;
        if (!empty($options)) {
            $this->option = array_merge($this->option, $options);
        }
        if (
            empty($this->option['clientId']) ||
            empty($this->option['clientSecret']) ||
            empty($this->option['scope']) ||
            empty($this->option['redirectUri']) ||
            empty($this->option['tenantId']) ||
            empty($this->option['oauthUrl']) ||
            empty($this->option['oauthAuthorizeEndpoint']) ||
            empty($this->option['oauthTokenEndpoint']) ||
            empty($this->option['graphApiEndpoint'])
        ) {
            throw new Exception('option config error');
        }
    }

    public function getOauthUrl() : string
    {
        return rtrim($this->option['oauthUrl'], '/') . '/' . trim($this->option['tenantId'], '/');
    }

    public function getTag(string $state) : array
    {
        $cache = $this->service->modx->getCacheManager();
        $session = $cache->get('tag_' . $state);
        if (!empty($session)) {
            $this->tag = array_merge($session, ['from' => 'session']);
        }
        return $this->tag;
    }

    /**
     * @throws Exception
     */
    public function getAuthorizationUrl(): string
    {
        $this->api = $this->getOauthUrl();
        $state = $codeVerifier = $this->generateCodeVerifier();
        if (strtolower($this->service->getOption('method', [], 'oauth')) === 'oidc') {
            $this->setTag(['state'=>$state, 'codeVerifier'=>$codeVerifier]);
            $query = [
                'client_id' => $this->option['clientId'],
                'response_type' => 'id_token token',
                'redirect_uri'  => $this->option['redirectUri'],
                'response_mode' => 'form_post',
                'scope'         => $this->option['scope'],
                'nonce'         => $state,
                'state'         => $state,
            ];
        } else {
            $codeChallenge = $this->generateCodeChallenge($codeVerifier);
            $this->setTag(['state'=>$state, 'codeVerifier'=>$codeVerifier, 'codeChallenge'=>$codeChallenge]);
            $query = [
                'client_id' => $this->option['clientId'],
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
                'response_type' => 'code',
                'redirect_uri'  => $this->option['redirectUri'],
                'response_mode' => 'form_post',
                'scope'         => $this->option['scope'],
                'state'         => $state,
            ];
        }
        return $this->api .
            '/' .
            trim($this->option['oauthAuthorizeEndpoint'], '/') .
            '/?' .
            http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    public function getAccessToken($authorizationCode, $codeVerifier)
    {
        $this->api = $this->getOauthUrl();
        $path = '/' .
            trim($this->option['oauthTokenEndpoint'], '/') .
            '/';
        $params = [
            'client_id' => $this->option['clientId'],
            'client_secret' => $this->option['clientSecret'],
            'code'      => $authorizationCode,
            'code_verifier' => $codeVerifier,
            'grant_type'   => 'authorization_code',
            'redirect_uri' => $this->option['redirectUri'],
            'scope'         => $this->option['scope'],
        ];
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        $chOpts = [
            CURLOPT_USERAGENT => $this->getUserAgent()
        ];
        return $this->curl($path, 'POST', $params, $headers, $chOpts);
    }


    public function getAccessTokenRefresh($refreshToken)
    {
        $this->api = $this->getOauthUrl();
        $path = '/' .
            trim($this->option['oauthTokenEndpoint'], '/') .
            '/';
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        $params = [
            'client_id' => $this->option['clientId'],
            'client_secret' => $this->option['clientSecret'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];
        $chOpts = [
            CURLOPT_USERAGENT => $this->getUserAgent()
        ];
        return $this->curl($path, 'POST', $params, $headers, $chOpts);
    }

    private function getUserAgent(): string
    {
        $serviceVersion = $this->service::VERSION;
        $version = $this->service->modx->getVersionData();
        $editor = "MODX; Revolution; rv:" . $version['full_version'];
        return "EntraLogin/$serviceVersion ($editor)";
    }

    public function setAccessToken(array $accessToken): void
    {
        $this->accessToken = $accessToken;
        $_SESSION['elog_access_token'] = $accessToken;
    }

    public function isTokenExpired(): bool
    {
        if (!isset($this->token['created']) || !isset($this->token['expires_in'])) {
            return true;
        }
        $now = strtotime('now');
        if (($now - $this->token['created']) < $this->token['expires_in']) {
            return false;
        }
        return true;
    }

    private function setTag(array $tag)
    {
        $this->tag = $tag;
        $cache = $this->service->modx->getCacheManager();
        $cache->set('tag_' . $tag['state'], $this->tag);
    }

    /**
     * @throws Exception
     */
    public function me(string $path = '', array $params = [], $method = 'GET')
    {
        if (empty($this->accessToken)) {
            throw new Exception('access token is empty');
        }
        $this->api = $this->option['graphApiEndpoint'];
        $headers = [
            'Authorization: Bearer ' . $this->accessToken['token'],
        ];
        $chOpts = [
            CURLOPT_USERAGENT => $this->getUserAgent()
        ];
        return $this->curl($path, $method, $params, $headers, $chOpts);
    }

    /**
     * @throws Exception
     */
    protected function generateCodeVerifier($length = 64): string
    {
        return substr(strtr(base64_encode(random_bytes($length)), '+/', '-_'), 0, $length);
    }

    protected function generateCodeChallenge($codeVerifier): string
    {
        return trim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }

    /**
     * @throws PhpfastcacheExtensionNotInstalledException
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheLogicException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheDriverException
     */
    public function isIdTokenValid(string $idToken): bool
    {
        // keys https://login.microsoftonline.com/{tenant}/discovery/v2.0/keys
        $keyStore = $this->getOauthUrl() . '/discovery/v2.0/keys';
        $httpClient = new \GuzzleHttp\Client();
        $httpFactory = new \GuzzleHttp\Psr7\HttpFactory();
        $cacheItemPool = \Phpfastcache\CacheManager::getInstance('files');
        $keySet = new CachedKeySet(
            $keyStore,
            $httpClient,
            $httpFactory,
            $cacheItemPool,
            null, // $expiresAfter int seconds to set the JWKS to expire
            true,  // $rateLimit    true to enable rate limit of 10 RPS on lookup of invalid keys
            'RS256'
        );
        try {
            $decoded = JWT::decode($idToken, $keySet);
            $this->getTag($decoded->nonce);
            return (!empty($this->tag));
        } catch (\Exception $e) {
            $this->service->modx->log(\XPDO::LOG_LEVEL_ERROR, 'JWT Error: ' . $e->getMessage());
            return false;
        }
    }
}