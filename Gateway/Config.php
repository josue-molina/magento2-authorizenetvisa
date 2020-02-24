<?php

namespace Pronko\AuthorizenetVisa\Gateway;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;

class Config
{
    /**
     * @var ValueHandlerPoolInterface
     */
    private $valueHandlerPool;

    public function __construct(
        ValueHandlerPoolInterface $valueHandlerPool
    )
    {
        $this->valueHandlerPool = $valueHandlerPool;
    }

    public function isSandbox()
    {
        return (bool)$this->getValue('is_sandbox');
    }

    public function getSdkUrl()
    {
        if ($this->isSandbox()) {
            return (string)$this->getValue('sdk_url_sandbox');
        }

        return (string)$this->getValue('sdk_url');
    }

    public function getCheckoutButtonSrc()
    {
        if ($this->isSandbox()) {
            return (string)$this->getValue('checkout_button_src_sandbox');
        }

        return (string)$this->getValue('checkout_button_src');
    }

    public function getPaymentCardSrc()
    {
        return (string)$this->getValue('payment_card_src');
    }

    public function getTitle()
    {
        return (string)$this->getValue('title');
    }

    public function getMerchantSourceId()
    {
        return (string)$this->getValue('merchant_source_id');
    }

    public function getApiKey()
    {
        if ($this->isSandbox()) {
            return (string)$this->getValue('api_key_sandbox');
        }
        return (string)$this->getValue('api_key');
    }

    public function getReviewMessage()
    {
        return (string)$this->getValue('review_message');
    }

    public function getButtonActionTittle()
    {
        return (string)$this->getValue('button_action_tittle');
    }

    public function getDisplayName()
    {
        return (string)$this->getValue('display_name');
    }

    public function getIsCollectShipping()
    {
        return (string)$this->getValue('is_collect_shipping');
    }

    public function getValue($field)
    {

        try {
            $handler = $this->valueHandlerPool->get($field);
            return $handler->handle(['field' => $field]);
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
