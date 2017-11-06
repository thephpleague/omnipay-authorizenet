<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMPaymentPlanQueryRequest extends AIMAbstractQueryRequest
{
    protected $action = '';
    protected $requestType = 'ARBGetSubscriptionRequest';
    protected $recurringReference;

    /**
     * @return string
     */
    public function getRecurringReference()
    {
        return $this->recurringReference;
    }

    /**
     * @param string $recurringReference
     */
    public function setRecurringReference($recurringReference)
    {
        $this->recurringReference = $recurringReference;
    }

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        $data->subscriptionId = $this->getRecurringReference();
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

        return $this->response = new AIMPaymentPlanQueryResponse($this, $httpResponse->getBody());
    }
}
