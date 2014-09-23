<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create payment profile Response
 */
class CIMCreatePaymentProfileResponse extends CIMAbstractResponse
{
    protected $xmlRootElement = 'createCustomerPaymentProfileResponse';

    public function getCustomerPaymentProfileId()
    {
        return (string)$this->data->customerPaymentProfileId;
    }
}
