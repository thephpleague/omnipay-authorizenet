<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create payment profile Response
 */
class CIMCreatePaymentProfileResponse extends CIMAbstractResponse
{
    protected $responseType = 'createCustomerPaymentProfileResponse';

    public function getCustomerProfileId()
    {
        if ($this->isSuccessful()) {
            return $this->request->getCustomerProfileId();
        }
        return null;
    }

    public function getCustomerPaymentProfileId()
    {
        if ($this->isSuccessful()) {
            return $this->data['customerPaymentProfileId'];
        }
        return null;
    }
}
