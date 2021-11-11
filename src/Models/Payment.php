<?php declare(strict_types=1);

namespace Momo\SDK\Models;

class Payment
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $orderId;

    /**
     * @var int
     */
    public $amount;

    /**
     * @var string
     */
    public $payUrl;

    /**
     * @var string
     */
    public $deepLink;

    /**
     * @var string
     */
    public $qrCodeUrl;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $updatedAt;

    public function __construct($id, $status, $orderId, $amount, $payUrl, $deepLink, $qrCodeUrl, $type, $createdAt, $updatedAt)
    {
        $this->id = $id;
        $this->status = $status;
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->payUrl = $payUrl;
        $this->deepLink = $deepLink;
        $this->qrCodeUrl = $qrCodeUrl;
        $this->type = $type;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array $array
     * @return \Momo\SDK\Models\Payment
     */
    static public function fromArray($array)
    {
        return new Payment(
            @$array['id'],
            @$array['status'],
            @$array['order_id'],
            @$array['amount'],
            @$array['pay_url'],
            @$array['deeplink'],
            @$array['qr_code_url'],
            @$array['type'],
            @$array['created_at'],
            @$array['updated_at']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'pay_url' => $this->payUrl,
            'deeplink' => $this->deepLink,
            'qr_code_url' => $this->qrCodeUrl,
            'type' => $this->type,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
