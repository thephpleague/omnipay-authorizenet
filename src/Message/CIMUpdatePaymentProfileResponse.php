<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create payment profile Response
 */
class CIMUpdatePaymentProfileResponse extends CIMCreatePaymentProfileResponse
{
    protected $responseType = 'updateCustomerPaymentProfileResponse';

    public function getCustomerPaymentProfileId()
    {
        return $this->request->getCustomerPaymentProfileId();
    }
}
