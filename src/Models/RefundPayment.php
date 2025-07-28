<?php declare (strict_types = 1);

namespace Momo\SDK\Models;

use Illuminate\Support\Collection;

class RefundPayment
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $paymentId;

    /**
     * @var string
     */
    public $orderId;

    /**
     * @var string
     */
    public $transId;

    /**
     * @var int
     */
    public $amount;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $updatedAt;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * @var Collection
     */
    public $refundPaymentStatusLogs;

    public function __construct($id, $paymentId, $orderId, $transId, $amount, $description, $status, $createdAt, $updatedAt, $payment, $refundPaymentStatusLogs)
    {
        $this->id = $id;
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->transId = $transId;
        $this->amount = $amount;
        $this->description = $description;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->payment = $payment;
        $this->refundPaymentStatusLogs = $refundPaymentStatusLogs;
    }

    /**
     * @param array $array
     * @return \Momo\SDK\Models\RefundPayment
     */
    public static function fromArray($array)
    {
        return new RefundPayment(
            @$array['id'],
            @$array['payment_id'],
            @$array['order_id'],
            @$array['trans_id'],
            @$array['amount'],
            @$array['description'],
            @$array['status'],
            @$array['created_at'],
            @$array['updated_at'],
            @$array['payment'] ? Payment::fromArray(@$array['payment']) : null,
            @$array['refund_payment_status_logs'] ?
            collect(
                array_map(function ($item) {
                    return RefundPaymentStatusLog::fromArray($item);
                }, @$array['refund_payment_status_logs'])
            )
            : null
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->paymentId,
            'order_id' => $this->orderId,
            'trans_id' => $this->transId,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'payment' => $this->payment?->toArray(),
            'refund_payment_status_logs' => $this->refundPaymentStatusLogs?->toArray(),
        ];
    }
}
