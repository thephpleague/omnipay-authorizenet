<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMPaymentPlansQueryRequest extends AIMAbstractRequest
{
    protected $action = '';
    protected $requestType = 'ARBGetSubscriptionListRequest';
    protected $limit = 1000;
    protected $offset = 1;

    /**
     * Get Limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set Limit.
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Get offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set offset.
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        $data->searchType = 'subscriptionActive';
        $data->sorting->orderBy = 'id';
        $data->sorting->orderDescending = true;
        $data->paging->limit = $this->getLimit();
        $data->paging->offset = $this->getOffset();
        return $data;
    }

    protected function addTransactionType(\SimpleXMLElement $data)
    {
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new AIMPaymentPlansQueryResponse($this, $httpResponse->getBody());
    }
}
