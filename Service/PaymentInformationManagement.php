<?php

namespace Pronko\AuthorizenetVisa\Service;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Pronko\AuthorizenetVisa\Api\PaymentInformationManagementInterface;
use Psr\Log\LoggerInterface;

class PaymentInformationManagement implements PaymentInformationManagementInterface
{
    private $logger;

    private $session;

    private $commandPool;

    private $paymentDataObjectFactory;

    public function __construct(
        LoggerInterface $logger,
        Session $session,
        CommandPoolInterface $commandPool,
        PaymentDataObjectFactory $paymentDataObjectFactory
    )
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->commandPool = $commandPool;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;

    }

    public function savePaymentInformation($response)
    {
        $order = $this->session->getLastRealOrder();
        if (!$order->getId()) {
            throw new LocalizedException(__('Order does not exist.'));
        }

        $arguments = [
            'response' => json_decode($response, true),
            'payment' => $this->paymentDataObjectFactory->create($order->getPayment())
        ];

        try {
            $this->commandPool->get('visa_complete')->execute($arguments);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new CouldNotSaveException(
                __('An error occurred on the server. Please try to place the order again.'),
                $e
            );
        }

        return true;
    }
}
