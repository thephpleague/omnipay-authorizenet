<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Void Request
 */
class CIMVoidRequest extends CIMAbstractRequest
{
    protected $xmlRootElement = 'createCustomerProfileTransactionRequest';
    protected $action = 'voidTransaction';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data->transaction->profileTransVoid->transId = $this->getTransactionReference();

        return $data;
    }
}
