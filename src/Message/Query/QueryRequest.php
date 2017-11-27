<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;

/**
 * Authorize.Net AIM Authorize Request
 */
class QueryRequest extends QueryBatchRequest
{
    protected $startTimestamp;
    protected $endTimestamp;

    /**
     * @return mixed
     */
    public function getStartTimestamp()
    {
        return $this->startTimestamp;
    }

    /**
     * @param mixed $startTimestamp
     */
    public function setStartTimestamp($startTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
    }

    /**
     * @return mixed
     */
    public function getEndTimestamp()
    {
        return $this->endTimestamp;
    }

    /**
     * @param mixed $endTimestamp
     */
    public function setEndTimestamp($endTimestamp)
    {
        $this->endTimestamp = $endTimestamp;
    }

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        if ($this->getStartTimestamp()) {
            $data->firstSettlementDate = date('Y-m-d\Th:i:s\Z', $this->getStartTimestamp());
            $data->lastSettlementDate = date('Y-m-d\Th:i:s\Z');
        }
        if ($this->getEndTimestamp()) {
            $data->lastSettlementDate = date('Y-m-d\Th:i:s\Z', $this->getEndTimestamp());
        }
        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        $this->response = new QueryResponse($this, $httpResponse->getBody());
        return $this->response;
    }
}
