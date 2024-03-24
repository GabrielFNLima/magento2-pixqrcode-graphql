<?php

declare(strict_types=1);

namespace GFNL\PixQrCodeGraphQL\Model\DataProvider;

use GFNL\PixQrCode\Model\Method\PixQrCode;
use Magento\Sales\Model\Order;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

class PaymentAdditionalInformation
{

    /**
     * @var Order
     */
    protected $orderModel;

    public function __construct(
        Order $orderModel
    ) {
        $this->orderModel = $orderModel;
    }

    /**
     * Retrieves additional information related to a specific order by its increment ID.
     *
     * @param string $incremmentId The increment ID of the order.
     * @return array
     * @throws GraphQlNoSuchEntityException If the order or payment method is not found.
     */
    public function getAdditionalInformation($incremmentId)
    {
        $result = [];
        $order = $this->getOrderByIncremmentId($incremmentId);

        if (!$order->getId()) {
            throw new GraphQlNoSuchEntityException(__('Order not found'));
        }

        $payment = $order->getPayment();

        if ($payment->getMethodInstance()->getCode() !== PixQrCode::CODE) {
            throw new GraphQlNoSuchEntityException(__('Payment method PixQrCode not found'));
        }

        if ($payment->getAdditionalInformation('payload_pix')) {
            $result['payload_pix'] = $payment->getAdditionalInformation('payload_pix');
        }

        if ($payment->getAdditionalInformation('qrcode')) {
            $result['qrcode'] = $payment->getAdditionalInformation('qrcode');
        }

        return $result;
    }

    /**
     * Retrieves an order by its increment ID.
     *
     * @param string $incremmentId The increment ID of the order to retrieve.
     * @return Order
     */
    private function getOrderByIncremmentId($incremmentId): Order
    {
        return $this->orderModel->loadByIncrementId($incremmentId);
    }
}
