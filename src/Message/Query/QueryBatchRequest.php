<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Authorize Request
 */
class QueryBatchRequest extends AIMAbstractQueryRequest
{
    protected $action = '';
    protected $requestType = 'getSettledBatchListRequest';

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->response = new QueryBatchResponse($this, $httpResponse->getBody()->getContents());
    }
}
