<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net DPM Purchase Request (aka "Authorize and Capture")
 */
class DPMPurchaseRequest extends DPMAuthorizeRequest
{
    protected $action = 'AUTH_CAPTURE';
}
