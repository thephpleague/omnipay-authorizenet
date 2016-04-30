<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net SIM Void Request
 */
class SIMVoidRequest extends SIMAuthorizeRequest
{
    protected $action = 'VOID';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data['x_trans_id'] = $this->getTransactionReference();

        return $data;
    }
}
