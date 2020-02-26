<?php

namespace Pronko\AuthorizenetVisa\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Payment;
use Psr\Log\LoggerInterface;

class UpdateOrderHandler implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderSender $orderSender,
        LoggerInterface $logger
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderSender = $orderSender;
        $this->logger = $logger;
    }

    public function handle(array $handlingSubject, array $response)
    {
        /** @var Payment $payment */
        $payment = $handlingSubject['payment']->getPayment();

        $baseTotalDue = $payment->getOrder()->getBaseTotalDue();
        $payment->registerCaptureNotification($baseTotalDue);

        if (!$payment->getOrder()->getEmailSent()) {
            try {
                $this->orderSender->send($payment->getOrder());
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        $this->orderRepository->save($payment->getOrder());
    }
}
