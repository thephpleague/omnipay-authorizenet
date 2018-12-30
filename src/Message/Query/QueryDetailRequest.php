<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Authorize Request
 */
class QueryDetailRequest extends QueryBatchRequest
{
    protected $action = '';
    protected $requestType = 'getTransactionDetailsRequest';
    protected $transactionReference;

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        $data->transId = $this->getTransactionReference();
        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->response = new QueryDetailResponse($this, $httpResponse->getBody()->getContents());
    }

    public function setTransactionReference($transactionReference)
    {
        $this->transactionReference = $transactionReference;
    }

    public function getTransactionReference()
    {
        return $this->transactionReference;
    }
}
