<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Capture Only Request
 */
class AIMCaptureOnlyRequest extends AIMAuthorizeRequest
{
    protected $action = 'captureOnlyTransaction';

    public function getData()
    {
        $this->validate('amount');
        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $this->addPayment($data);
        $data->transactionRequest->authCode = $this->getAuthCode();
        $this->addSolutionId($data);
        $this->addBillingData($data);
        $this->addCustomerIP($data);
        $this->addTransactionSettings($data);
        
        return $data;
    }
}
