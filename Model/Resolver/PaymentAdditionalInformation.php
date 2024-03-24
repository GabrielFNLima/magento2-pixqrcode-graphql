<?php

declare(strict_types=1);

namespace GFNL\PixQrCodeGraphQL\Model\Resolver;

use GFNL\PixQrCodeGraphQL\Model\DataProvider\PaymentAdditionalInformation as PaymentAdditionalInformationDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PaymentAdditionalInformation implements ResolverInterface
{

    /**
     * @var PaymentAdditionalInformationDataProvider
     */
    protected $additionalInformation;

    public function __construct(
        PaymentAdditionalInformationDataProvider $additionalInformation
    ) {
        $this->additionalInformation = $additionalInformation;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field       $field,
        $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        if (empty($args['order_number'])) {
            throw new GraphQlInputException(__('"order_number" value should be specified'));
        }
        try {
            $data = $this->additionalInformation->getAdditionalInformation($args['order_number']);
        } catch (LocalizedException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }

        return $data;
    }
}
