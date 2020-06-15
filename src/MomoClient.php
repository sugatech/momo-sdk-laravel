<?php

namespace Momo\SDK;

use Illuminate\Support\Arr;
use PassportClientCredentials\OAuthClient;
use Zttp\PendingZttpRequest;
use Zttp\Zttp;
use Zttp\ZttpResponse;

class MomoClient
{
    /**
     * @var OAuthClient
     */
    private $oauthClient;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->oauthClient = new OAuthClient(
            config('momo.oauth.url'),
            config('momo.oauth.client_id'),
            config('momo.oauth.client_secret')
        );
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param callable $handler
     * @return ZttpResponse
     */
    private function request($handler)
    {
        $request = Zttp::withHeaders([
            'Authorization' => 'Bearer ' . $this->oauthClient->getAccessToken(),
        ])
            ->withoutVerifying();

        $response = $handler($request);

        if ($response->status() == 401) {
            $this->oauthClient->getAccessToken(true);
        }

        return $response;
    }

    /**
     * @param string $route
     * @return string
     */
    private function getUrl($route)
    {
        return $this->apiUrl . '/api/client/v1' . $route;
    }
}