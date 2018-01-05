<?php

namespace OmnipayAuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Capture Only Request
 */
class AIMCaptureOnlyRequest extends AIMAbstractRequest
{
    protected $action = 'captureOnlyTransaction';

    public function getData()
    {
        $this->validate('amount');

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $data->transactionRequest->authCode = $this->getAuthCode();
        $this->addTransactionSettings($data);

        return $data;
    }
}
