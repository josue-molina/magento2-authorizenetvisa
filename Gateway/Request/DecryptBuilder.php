<?php

namespace Pronko\AuthorizenetVisa\Gateway\Request;


use Pronko\AuthorizenetVisa\Gateway\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DecryptBuilder implements BuilderInterface
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    public function build(array $buildSubject)
    {
        $response = $buildSubject['response'];

        $dataValue = $response['encPaymentData'] ?? null;
        $dataKey = $response['encKey'] ?? null;
        $transactionId = $response['callid'] ?? null;
        return [
            'decryptPaymentDataRequest' => [
                'merchantAuthentication' => [
                    'name' => $this->config->getApiLoginId(),
                    'transactionKey' => $this->config->getTransactionKey()
                ],
                'opaqueData' => [
                    'dataDescriptor' => 'COMMON.VCO.ONLINE.PAYMENT',
                    'dataValue' => $dataValue,
                    'dataKey' => $dataKey
                ],
                'callId' => $transactionId
            ]
        ];
    }
}
