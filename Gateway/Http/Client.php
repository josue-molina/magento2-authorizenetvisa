<?php

namespace Pronko\AuthorizenetVisa\Gateway\Http;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Monolog\Logger;
use Vault\Exceptions\ClientException;

class Client implements ClientInterface
{
    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        Logger $logger,
        ConverterInterface $converter
    )
    {
        $this->logger = $logger;
        $this->converter = $converter;
    }

    public function placeRequest(TransferInterface $transferObject)
    {
        $log = [
            'request_uri' => $transferObject->getUri(),
            'request' => $this->converter
                ? $this->converter->convert($transferObject->getBody())
                : $transferObject->getBody()
        ];

        $result = [];
        try {
            $curlRequest = curl_init($transferObject->getUri());
            curl_setopt($curlRequest, CURLOPT_POSTFIELDS, $transferObject->getBody());
            curl_setopt($curlRequest, CURLOPT_HEADER, 0);
            curl_setopt($curlRequest, CURLOPT_TIMEOUT, 45);
            curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlRequest, CURLOPT_SSL_VERIFYHOST, 2);
            $response = curl_exec($curlRequest);
            if ($curlError = curl_error($curlRequest)) {
                throw new \Exception($curlError);
            }
            curl_close($curlRequest);
            $result = $this->converter
                ? $this->converter->convert($response)
                : $response->getBody();
            $log['response'] = $result;
        } catch (\Zend_Http_Client_Exception $e) {
            throw new ClientException(__($e->getMessage()));
        } catch (ConverterException $e) {
            throw $e;
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}
