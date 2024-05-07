<?php
/**
 * @copyright Copyright © Avarda. All rights reserved.
 * @package   Avarda_Checkout3
 */

namespace Avarda\Checkout3\Model;

use Avarda\Checkout3\Api\Data\PaymentDetailsInterface;
use Avarda\Checkout3\Api\Data\PaymentDetailsInterfaceFactory;
use Avarda\Checkout3\Api\PaymentManagementInterface;
use Avarda\Checkout3\Api\QuotePaymentManagementInterface;

/**
 * PaymentManagement
 * @see \Avarda\Checkout3\Api\PaymentManagementInterface
 */
class PaymentManagement implements PaymentManagementInterface
{
    protected PaymentDetailsInterfaceFactory $paymentDetailsFactory;
    protected QuotePaymentManagementInterface $quotePaymentManagement;

    public function __construct(
        PaymentDetailsInterfaceFactory $paymentDetailsFactory,
        QuotePaymentManagementInterface $quotePaymentManagement
    ) {
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->quotePaymentManagement = $quotePaymentManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchaseData($cartId, $renew = false)
    {
        $purchaseData = $this->quotePaymentManagement->getPurchaseData($cartId, $renew);
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPurchaseData($purchaseData);

        return $paymentDetails;
    }

    /**
     * {@inheritdoc}
     */
    public function freezeCart($cartId)
    {
        $this->quotePaymentManagement->setQuoteIsActive($cartId, false);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDetailsList($cartId)
    {
        return $this->quotePaymentManagement->getItemDetailsList($cartId);
    }
}
