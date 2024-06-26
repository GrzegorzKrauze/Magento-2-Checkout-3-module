<?php
/**
 * @copyright Copyright © Avarda. All rights reserved.
 * @package   Avarda_Checkout3
 */

namespace Avarda\Checkout3\Model;

use Avarda\Checkout3\Api\AvardaOrderRepositoryInterface;
use Avarda\Checkout3\Api\PaymentCompleteInterface;
use Avarda\Checkout3\Api\QuotePaymentManagementInterface;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Lock\LockManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class PaymentComplete implements PaymentCompleteInterface
{
    protected QuotePaymentManagementInterface $quotePaymentManagement;
    protected AvardaOrderRepositoryInterface $avardaOrderRepository;
    protected OrderRepositoryInterface $orderRepository;
    protected LoggerInterface $logger;
    protected LockManagerInterface $lockManager;

    public function __construct(
        QuotePaymentManagementInterface $quotePaymentManagement,
        AvardaOrderRepositoryInterface $avardaOrderRepository,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        LockManagerInterface $lockManager
    ) {
        $this->quotePaymentManagement = $quotePaymentManagement;
        $this->avardaOrderRepository = $avardaOrderRepository;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->lockManager = $lockManager;
    }

    /**
     * Handle completing order
     *
     * @param $purchaseId
     * @return string
     * @throws LocalizedException
     */
    public function execute($purchaseId): string
    {
        $wasLocked = false;
        try {
            try {
                // If cron/serverside call is processing order at the same time ignore the request
                if (!$this->lockManager->isLocked($purchaseId)) {
                    $wasLocked = true;
                    $this->lockManager->lock($purchaseId);

                    $orderId = $this->avardaOrderRepository->getByPurchaseId($purchaseId);
                    // No order found
                    if (!$orderId->getOrderId()) {
                        $this->logger->warning("No order found with '{$purchaseId}'");
                        return "";
                    }

                    $order = $this->orderRepository->get($orderId->getOrderId());
                    $this->quotePaymentManagement->updateOrderPaymentStatus($order);
                    $this->quotePaymentManagement->finalizeOrder($order);
                    return "OK";
                }
            } catch (PaymentException $e) {
                $this->logger->info("Payment complete handling did not succeed: " . $e->getMessage());
            }
        } catch (Exception $e) {
            $this->logger->error("Payment complete handling failed with error: " . $e->getMessage());
        } finally {
            if ($wasLocked) {
                $this->lockManager->unlock($purchaseId);
            }
        }

        return "";
    }
}
