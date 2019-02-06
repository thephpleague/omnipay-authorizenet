<?php

namespace Omnipay\AuthorizeNet\Message\Query;

/**
 * Authorize.Net AIM Authorize Request
 */

class QueryRequest extends QueryBatchRequest
{
    const DATE_TIME_FORMAT = 'Y-m-d\Th:i:s\Z';

    protected $startTimestamp;
    protected $endTimestamp;

    /**
     * @return int|null
     */
    public function getStartTimestamp()
    {
        return $this->startTimestamp;
    }

    /**
     * @param int|null $startTimestamp unix timestamp
     */
    public function setStartTimestamp($startTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
    }

    /**
     * @return int|null
     */
    public function getEndTimestamp()
    {
        return $this->endTimestamp;
    }

    /**
     * @param int|null $endTimestamp unix timestamp
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
            $data->firstSettlementDate = date(
                static::DATE_TIME_FORMAT,
                $this->getStartTimestamp()
            );
            $data->lastSettlementDate = date(static::DATE_TIME_FORMAT);
        }

        if ($this->getEndTimestamp()) {
            $data->lastSettlementDate = date(
                static::DATE_TIME_FORMAT,
                $this->getEndTimestamp()
            );
        }

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

        $this->response = new QueryResponse(
            $this,
            $httpResponse->getBody()->getContents()
        );

        return $this->response;
    }
}
