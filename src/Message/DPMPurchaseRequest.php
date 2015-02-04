<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net DPM Purchase Request (known as "Authorization and Capture")
 */
class DPMPurchaseRequest extends DPMAuthorizeRequest
{
    protected $action = 'AUTH_CAPTURE';
}
