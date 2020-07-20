<?php

namespace Momo\SDK;

use OAuth2ClientCredentials\OAuthClient;
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

    /**
     * @param $partnerRefId
     * @param $token
     * @param $amout
     * @param $userId
     * @param $phoneNumber
     * @return mixed
     */
    public function authorize($partnerRefId, $token, $amout, $userId, $phoneNumber)
    {
        $param = [
            'partner_ref_id' => $partnerRefId,
            'token' => $token,
            'amount' => $amout,
            'user_id' => $userId,
            'phone_number' => $phoneNumber,
        ];

        $response = $this->request(function (PendingZttpRequest $request) use ($param) {
            return $request->asJson()
                ->post($this->getUrl('/payment/app/authorize'), $param);
        });

        if (!$response->isSuccess()) {
            return false;
        }

        return $response->json();
    }

    /**
     * @param $transId
     * @return mixed
     */
    public function capture($transId)
    {
        $param = [
            'trans_id' => $transId
        ];

        $response = $this->request(function (PendingZttpRequest $request) use ($param) {
            return $request->asJson()
                ->post($this->getUrl('/payment/app/capture'), $param);
        });

        if (!$response->isSuccess()) {
            return false;
        }

        return $response->json();
    }
}