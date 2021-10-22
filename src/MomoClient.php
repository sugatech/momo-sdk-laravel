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
    public function authorizeAppInApp($partnerRefId, $token, $amount, $userId, $phoneNumber)
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
    public function captureAppInApp($paymentId)
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

    /**
     * @param int $userId
     * @param int $amount
     * @param string $orderId
     * @param string $redirectUrl
     * @param string $ipnUrl
     * @return array|false|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function createAllInOne($userId, $amount, $orderId, $redirectUrl, $ipnUrl)
    {
        $params = [
            'user_id' => $userId,
            'amount' => $amount,
            'order_info' => $orderId,
            'redirect_url' => $redirectUrl,
            'ipn_url' => $ipnUrl,
        ];

        $response = $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/payment/aio/create'), $params);
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json();
    }

    /**
     * @param int $userId
     * @param array $params
     * @return array|false|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function authorizeAllInOne($userId, $params)
    {
        $params = [
            'user_id' => $userId,
            'partner_code' => $params['partnerCode'] ?? null,
            'order_id' => $params['orderId'] ?? null,
            'request_id' => $params['requestId'] ?? null,
            'amount' => $params['amount'] ?? null,
            'order_info' => $params['orderInfo'] ?? null,
            'order_type' => $params['orderType'] ?? null,
            'transaction_id' => $params['transId'] ?? null,
            'result_code' => $params['resultCode'] ?? null,
            'message' => $params['message'] ?? null,
            'pay_type' => $params['payType'] ?? null,
            'response_time' => $params['responseTime'] ?? null,
            'extra_data' => $params['extraData'] ?? '',
            'signature' => $params['signature'] ?? null,
        ];

        $response = $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/payment/aio/authorize'), $params);
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json();
    }

    /**
     * @param int $userId
     * @param string $partnerCode
     * @param string $requestId
     * @param string $orderId
     * @param int $amount
     * @return array|false|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function captureAllInOne($userId, $partnerCode, $requestId, $orderId, $amount)
    {
        $params = [
            'user_id' => $userId,
            'partner_code' => $partnerCode,
            'request_id' => $requestId,
            'order_id' => $orderId,
            'amount' => $amount,
        ];

        $response = $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/payment/aio/capture'), $params);
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json();
    }
}