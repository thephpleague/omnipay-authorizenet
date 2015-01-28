<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Void Request
 */
class CIMVoidRequest extends CIMAbstractRequest
{
    protected $action = 'voidTransaction';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data->transactionRequest->refTransId = $this->getTransactionReference();
        $this->addTestModeSetting($data);

        return $data;
    }
}
