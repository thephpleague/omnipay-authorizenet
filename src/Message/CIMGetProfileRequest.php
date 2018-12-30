<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Create Request to get customer profile
 */
class CIMGetProfileRequest extends CIMAbstractRequest
{
    protected $requestType = 'getCustomerProfileRequest';

    public function getData()
    {
        $this->validate('customerProfileId');

        $data = $this->getBaseData();

        $data->customerProfileId = $this->getCustomerProfileId();

        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->response = new CIMGetProfileResponse($this, $httpResponse->getBody()->getContents());
    }
}
