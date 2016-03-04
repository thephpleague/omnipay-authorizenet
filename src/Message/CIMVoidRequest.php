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
        $transRef = json_decode($this->getTransactionReference(), true);
        if (is_array($transRef) && isset($transRef['transId'])) {
            $data->transaction->profileTransVoid->transId = $transRef["transId"];
        }

        return $data;
    }
}
