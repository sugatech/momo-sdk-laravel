<?php

namespace Momo\SDK;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use OAuth2ClientCredentials\OAuthClient;

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
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function request($handler)
    {
        $request = Http::withHeaders([
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
     * @param $amount
     * @param $userId
     * @param $phoneNumber
     * @return array | bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function authorize($partnerRefId, $token, $amount, $userId, $phoneNumber)
    {
        $param = [
            'partner_ref_id' => $partnerRefId,
            'token' => $token,
            'amount' => $amount,
            'user_id' => $userId,
            'phone_number' => $phoneNumber,
        ];

        $response = $this->request(function (PendingRequest $request) use ($param) {
            return $request->asJson()
                ->post($this->getUrl('/payment/app/authorize'), $param);
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json();
    }

    /**
     * @param $paymentId
     * @return array | bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function capture($paymentId)
    {
        $response = $this->request(function (PendingRequest $request) use ($paymentId) {
            return $request->asJson()
                ->post($this->getUrl('/payment/app/capture/' . $paymentId));
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json();
    }
}