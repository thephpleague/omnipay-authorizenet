<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create card Response
 */
class CIMCreateCardResponse extends CIMAbstractResponse
{
    protected $xmlRootElement = 'createCustomerProfileResponse';

    public function getCustomerProfileId()
    {
        if ($this->isSuccessful()) {
            return (string)$this->data->customerProfileId;
        }
        return null;
    }

    public function getCustomerPaymentProfileId()
    {
        if ($this->isSuccessful()) {
            return (string)$this->data->customerPaymentProfileIdList->numericString;
        }
        return null;
    }
}
