<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net Capture Request
 */
class AIMCaptureRequest extends AIMAbstractRequest
{
    protected $action = 'priorAuthCaptureTransaction';

    public function getData()
    {
        $this->validate('amount', 'transactionReference');

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $data->transactionRequest->refTransId = $this->getTransactionReference()->getTransId();
        $this->addTransactionSettings($data);

        return $data;
    }
}
