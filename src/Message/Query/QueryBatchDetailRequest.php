<?php

namespace Omnipay\AuthorizeNet\Message\Query;

/**
 * Authorize.Net AIM Authorize Request
 */

class QueryBatchDetailRequest extends QueryBatchRequest
{
    protected $action = '';
    protected $requestType = 'getTransactionListRequest';
    protected $limit = 1000;
    protected $offset = 1;
    protected $batchID;

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        $data->batchId = $this->getBatchID();
        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            $headers,
            $data
        );

        return $this->response = new QueryBatchDetailResponse(
            $this,
            $httpResponse->getBody()->getContents()
        );
    }

    public function setBatchID($batchID)
    {
        $this->batchID = $batchID;
    }

    public function getBatchID()
    {
        return $this->batchID;
    }
}
