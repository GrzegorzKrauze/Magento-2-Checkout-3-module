<?php
/**
 * @copyright Copyright © Avarda. All rights reserved.
 * @package   Avarda_Checkout3
 */

namespace Avarda\Checkout3\Api;

use Avarda\Checkout3\Api\Data\ItemDetailsListInterface;
use Magento\Framework\Exception\PaymentException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface for managing Avarda payment information
 *
 * @api
 */
interface QuotePaymentManagementInterface
{
    /**
     * Get purchase ID for quote
     *
     * @param string|int $cartId
     * @param bool $renew
     * @return string
     */
    public function getPurchaseData($cartId, bool $renew = false);

    /**
     * Make Avarda InitializePurchase call and return purchase ID
     *
     * @param CartInterface|Quote $quote
     * @return string
     */
    public function initializePurchase(CartInterface $quote);

    /**
     * Get quote items additional information not provided by Magento
     *
     * @param string|int $cartId
     * @return ItemDetailsListInterface
     */
    public function getItemDetailsList($cartId);

    /**
     * Make Avarda UpdateItems call and return purchase ID
     *
     * @param CartInterface|Quote $quote
     * @return void
     */
    public function updateItems(CartInterface $quote);

    /**
     * Setting the quote is_active to false hides it from the frontend and
     * renders the customer unable to manipulate the cart while payment is
     * processed.
     *
     * @param string|int $cartId
     * @param bool $isActive
     * @return void
     */
    public function setQuoteIsActive($cartId, $isActive);

    /**
     * Update order (quote) from Avarda
     *
     * @param CartInterface|string|int $cartId
     * @return void
     */
    public function updatePaymentStatus($cartId);

    /**
     * Update order (quote) payment status from Avarda.
     *
     * @param CartInterface|string|int $quote
     * @return void
     */
    public function updateOnlyPaymentStatus($quote);

    /**
     * Update order payment status and info from Avarda.
     *
     * @param OrderInterface $order
     * @return void
     */
    public function updateOrderPaymentStatus($order);

    /**
     * Update order payment status from Avarda.
     *
     * @param OrderInterface $order
     * @return void
     */
    public function updateOnlyOrderPaymentStatus($order);

    /**
     * Get quote ID by Avarda purchase ID
     *
     * @param string $purchaseId
     * @return int
     * @throws PaymentException
     */
    public function getQuoteIdByPurchaseId($purchaseId);
}
