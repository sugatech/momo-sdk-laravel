<?php declare (strict_types = 1);

namespace Momo\SDK\Models;

class RefundPaymentStatusLog
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $refundPaymentId;

    /**
     * @var string
     */
    public $oldStatus;

    /**
     * @var string
     */
    public $status;

    /**
     * @var object
     */
    public $response;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $updatedAt;

    /**
     * @var RefundPayment
     */
    public $refundPayment;

    public function __construct($id, $refundPaymentId, $oldStatus, $status, $response, $createdAt, $updatedAt, $refundPayment)
    {
        $this->id = $id;
        $this->refundPaymentId = $refundPaymentId;
        $this->oldStatus = $oldStatus;
        $this->status = $status;
        $this->response = $response;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->refundPayment = $refundPayment;
    }

    /**
     * @param array $array
     * @return \Momo\SDK\Models\RefundPaymentStatusLog
     */
    public static function fromArray($array)
    {
        return new RefundPaymentStatusLog(
            @$array['id'],
            @$array['refund_payment_id'],
            @$array['old_status'],
            @$array['status'],
            @$array['response'],
            @$array['created_at'],
            @$array['updated_at'],
            @$array['refund_payment'] ? RefundPayment::fromArray(@$array['refund_payment']) : null
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'refund_payment_id' => $this->refundPaymentId,
            'old_status' => $this->oldStatus,
            'status' => $this->status,
            'response' => $this->response,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'refund_payment' => $this->refundPayment?->toArray(),
        ];
    }
}
