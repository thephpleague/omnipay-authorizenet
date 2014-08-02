<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net AIM Void Request
 */
class AIMVoidRequest extends SIMAbstractRequest
{
    protected $action = 'voidTransaction';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = parent::getData();
        $data->transactionRequest->refTransId = $this->getTransactionReference();

        return $data;
    }
}
