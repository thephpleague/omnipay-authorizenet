<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Delete payment profile Response
 */
class CIMDeletePaymentProfileResponse extends CIMAbstractResponse
{
    protected $responseType = 'deleteCustomerPaymentProfileResponse';
}
