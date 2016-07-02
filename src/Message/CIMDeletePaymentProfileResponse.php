<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Delete payment profile Response
 */
class CIMDeletePaymentProfileResponse extends CIMCreatePaymentProfileResponse
{
    protected $responseType = 'deleteCustomerPaymentProfileResponse';

    public function getCustomerPaymentProfileId()
    {
        return $this->request->getCustomerPaymentProfileId();
    }
}
