<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Message\SIMAbstractRequest;

/**
 * Authorize.Net DPM Authorize and Capture (aka Payment) Request
 */
class DPMPaymentRequest extends DPMAuthorizeRequest
{
    protected $action = 'AUTH_CAPTURE';
}
