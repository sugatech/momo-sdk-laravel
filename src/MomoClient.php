<?php

namespace Momo\SDK;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Momo\SDK\Models\AllInOneIpnResponse;
use Momo\SDK\Models\Payment;
use Momo\SDK\Models\RefundPayment;
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
     * @return \Illuminate\Http\Client\Response
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
     * @param string $orderId
     * @param int $amount
     * @param string $orderInfo
     * @param string $redirectUrl
     * @param string|null $ipnUrl
     * @return false|\Momo\SDK\Models\Payment
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function createAllInOne($orderId, $amount, $orderInfo, $redirectUrl, $ipnUrl = null)
    {
        $params = [
            'order_id' => $orderId,
            'amount' => $amount,
            'order_info' => $orderInfo,
            'redirect_url' => $redirectUrl,
            'ipn_url' => $ipnUrl ?: config('momo.aio.ipn_url'),
        ];

        $response = $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/payment/aio/create'), $params);
        });

        if (!$response->successful()) {
            return false;
        }

        return Payment::fromArray($response->json());
    }

    /**
     * @param array $params
     * @return false|\Momo\SDK\Models\AllInOneIpnResponse
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function ipnAllInOne($params)
    {
        $params = [
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
                ->post($this->getUrl('/payment/aio/ipn'), $params);
        });

        if (!$response->successful()) {
            return false;
        }

        return AllInOneIpnResponse::fromArray($response->json());
    }

    /**
     * @param string $partnerCode
     * @param string $orderId
     * @param int $amount
     * @return false|\Momo\SDK\Models\Payment
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function captureAllInOne($partnerCode, $orderId, $amount)
    {
        $params = [
            'partner_code' => $partnerCode,
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

        return Payment::fromArray($response->json());
    }

    public function refundAllInOne($orderId, $paymentOrderId, $amount, $description)
    {
        $params = [
            'order_id' => $orderId,
            'payment_order_id' => $paymentOrderId,
            'amount' => $amount,
            'description' => $description,
        ];

        $response = $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/payment/aio/refund'), $params);
        });

        if (!$response->successful()) {
            return false;
        }

        return RefundPayment::fromArray($response->json());
    }

    public function getPaymentAllInOne($orderId)
    {
        $response = $this->request(function (PendingRequest $request) use ($orderId) {
            return $request->get($this->getUrl('/payment/aio/payments/id/' . $orderId));
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json() ? Payment::fromArray($response->json()) : null;
    }

    public function getRefundPaymentAllInOne($orderId)
    {
        $response = $this->request(function (PendingRequest $request) use ($orderId) {
            return $request->get($this->getUrl('/payment/aio/payments/refunds/id/' . $orderId));
        });

        if (!$response->successful()) {
            return false;
        }

        return $response->json() ? RefundPayment::fromArray($response->json()) : null;
    }
}
