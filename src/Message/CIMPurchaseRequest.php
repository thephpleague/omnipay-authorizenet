<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Creates a Authorize capture transaction request for the specified card
 */
class CIMPurchaseRequest extends CIMAuthorizeRequest
{
    protected $action = "authCaptureTransaction";
}
