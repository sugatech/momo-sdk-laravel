<?php declare(strict_types=1);

namespace Momo\SDK\Models;

class AllInOneIpnResponse
{
    public const RESPONSE_STATUS_COMPLETE_PAYMENT = 0;
    public const RESPONSE_STATUS_WAITING_CONFIRM_PAYMENT = 90;
    public const RESPONSE_STATUS_INVALID_PAYMENT = 100;
    public const RESPONSE_STATUS_INVALID_PAYMENT_STATUS = 200;
    public const RESPONSE_STATUS_INVALID_PAYMENT_AMOUNT = 300;
    public const RESPONSE_STATUS_WAITING_PAYMENT = 500;
    public const RESPONSE_STATUS_INVALID_PARTNER_CODE = 501;
    public const RESPONSE_STATUS_INVALID_SIGNATURE = 502;

    /**
     * @var string
     */
    public $partnerCode;

    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $orderId;

    /**
     * @var int
     */
    public $resultCode;

    /**
     * @var string
     */
    public $message;

    /**
     * @var int
     */
    public $responseTime;

    /**
     * @var string
     */
    public $extraData;

    /**
     * @var string
     */
    public $signature;

    public function __construct($partnerCode, $requestId, $orderId, $resultCode, $message, $responseTime, $extraData, $signature)
    {
        $this->partnerCode = $partnerCode;
        $this->requestId = $requestId;
        $this->orderId = $orderId;
        $this->resultCode = $resultCode;
        $this->message = $message;
        $this->responseTime = $responseTime;
        $this->extraData = $extraData;
        $this->signature = $signature;
    }

    /**
     * @param array $array
     * @return \Momo\SDK\Models\AllInOneIpnResponse
     */
    static public function fromArray($array)
    {
        return new AllInOneIpnResponse(
            @$array['partnerCode'],
            @$array['requestId'],
            @$array['orderId'],
            @$array['resultCode'],
            @$array['message'],
            @$array['responseTime'],
            @$array['extraData'],
            @$array['signature']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'partnerCode' => $this->partnerCode,
            'requestId' => $this->requestId,
            'orderId' => $this->orderId,
            'resultCode' => $this->resultCode,
            'message' => $this->message,
            'responseTime' => $this->responseTime,
            'extraData' => $this->extraData,
            'signature' => $this->signature,
        ];
    }

    /**
     * @return bool
     */
    public function isInvalidPayment()
    {
        return $this->resultCode == self::RESPONSE_STATUS_INVALID_PAYMENT;
    }

    /**
     * @return bool
     */
    public function isInvalidPaymentStatus()
    {
        return $this->resultCode == self::RESPONSE_STATUS_INVALID_PAYMENT_STATUS;
    }

    /**
     * @return bool
     */
    public function isInvalidPaymentAmount()
    {
        return $this->resultCode == self::RESPONSE_STATUS_INVALID_PAYMENT_AMOUNT;
    }

    /**
     * @return bool
     */
    public function isInvalidPartnerAmount()
    {
        return $this->resultCode == self::RESPONSE_STATUS_INVALID_PARTNER_CODE;
    }

    /**
     * @return bool
     */
    public function isInvalidSignature()
    {
        return $this->resultCode == self::RESPONSE_STATUS_INVALID_SIGNATURE;
    }

    /**
     * @return bool
     */
    public function isWaitingConfirmPayment()
    {
        return $this->resultCode == self::RESPONSE_STATUS_WAITING_CONFIRM_PAYMENT;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->resultCode == self::RESPONSE_STATUS_COMPLETE_PAYMENT;
    }

    /**
     * @return bool
     */
    public function isWaitingPayment()
    {
        return $this->resultCode == self::RESPONSE_STATUS_WAITING_PAYMENT;
    }
}
