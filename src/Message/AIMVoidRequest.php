<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net AIM Void Request
 */
class AIMVoidRequest extends AIMAbstractRequest
{
    protected $action = 'voidTransaction';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data->transactionRequest->refTransId = $this->getTransactionReference()->getTransId();
        $this->addTransactionSettings($data);

        return $data;
    }
}
