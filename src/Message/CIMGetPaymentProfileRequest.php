<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Create Request to get customer profile
 */
class CIMGetPaymentProfileRequest extends CIMAbstractRequest
{
    protected $xmlRootElement = 'getCustomerPaymentProfileRequest';

    public function getData()
    {
        $this->validate('customerProfileId', 'customerPaymentProfileId');

        $data = $this->getBaseData();

        $data->customerProfileId = $this->getCustomerProfileId();
        $data->customerPaymentProfileId = $this->getCustomerPaymentProfileId();

        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new CIMGetPaymentProfileResponse($this, $httpResponse->getBody());
    }

}
