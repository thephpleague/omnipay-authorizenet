<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create payment profile Response
 */
class CIMUpdatePaymentProfileResponse extends CIMCreatePaymentProfileResponse
{
    protected $xmlRootElement = 'updateCustomerPaymentProfileResponse';

    public function getCustomerPaymentProfileId()
    {
        return $this->request->getCustomerPaymentProfileId();
    }
}
