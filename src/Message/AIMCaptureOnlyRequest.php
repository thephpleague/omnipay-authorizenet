<?php

namespace OmnipayAuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Capture Only Request
 */
class AIMCaptureOnlyRequest extends AIMAuthorizeRequest
{
    protected $action = 'captureOnlyTransaction';

    public function getData()
    {
        $data = parent::getData();

        $data->transactionRequest->authCode = $this->getAuthCode();

        return $data;
    }
}
