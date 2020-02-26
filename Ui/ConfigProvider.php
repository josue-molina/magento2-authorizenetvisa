<?php

namespace Pronko\AuthorizenetVisa\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Model\Quote;
use Pronko\AuthorizenetVisa\Gateway\Config;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Repository
     */
    private $assetRepo;
    /**
     * @var Quote
     */
    private $quote;

    public function __construct(
        Config $config,
        UrlInterface $url,
        Repository $repository,
        RequestInterface $request,
        Session $session
    )
    {
        $this->config = $config;
        $this->url = $url;
        $this->assetRepo = $repository;
        $this->request = $request;
        $this->quote = $session->getQuote();
    }

    public function getConfig()
    {
        $visaCheckoutButtonSrc = $this->config->getCheckoutButtonSrc() . '?cardBrands=VISA,MASTERCARD,DISCOVER,AMEX';
        return [
            'payment' => [
                'pronko_authorizenet' => [
                    'title' => $this->config->getTitle(),
                    'sdkUrl' => $this->config->getSdkUrl(),
                    'paymentCardSrc' => $this->config->getPaymentCardSrc(),
                    'visaCheckoutButtonSrc' => $visaCheckoutButtonSrc,
                    'visaCheckoutInitialSettings' => [
                        'apiKey' => $this->config->getApiKey(),
                        'sourceId' => $this->config->getMerchantSourceId(),
                        'settings' => [
                            'locale' => 'en_US',
                            'country' => 'US',
                            'displayName' => $this->config->getDisplayName(),
                            'logoUrl' => $this->getLogoUrl(),
                            'websiteUrl' => $this->url->getBaseUrl(),
                            'shipping' => [
                                'acceptedRegions' => ['USA', 'CA'],
                                'collectShipping' => $this->config->getIsCollectShipping()
                            ],
                            'review' => [
                                'message' => $this->config->getReviewMessage(),
                                'buttonAction' => $this->config->getButtonActionTittle()
                            ],
                            'dataLevel' => 'SUMMARY'
                        ],
                        'paymentRequest' => [
                            'merchantRequestId' => $this->quote->getId(),
                            'currencyCode' => $this->quote->getBaseCurrencyCode(),
                            'subtotal' => $this->quote->getBaseSubtotal(),
                            'total' => $this->quote->getBaseGrandTotal()
                        ]
                    ]
                ]
            ]
        ];
    }

    private function getLogoUrl()
    {
        return $this->getViewFileUrl('images/logo.svg');
    }

    public function getViewFileUrl($fieldId)
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()]);
            return $this->assetRepo->getUrlWithParams($fieldId, $params);
        } catch (\Exception $e) {
            return '';
        }
    }
}
