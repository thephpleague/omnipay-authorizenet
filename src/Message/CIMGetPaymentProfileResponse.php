<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Get payment profiles Response
 */
class CIMGetPaymentProfileResponse extends CIMCreateCardResponse
{
    protected $xmlRootElement = 'getCustomerPaymentProfileResponse';

    public function getCardReference()
    {
        $cardRef = null;
        if ($this->isSuccessful() && $this->request->getCustomerProfileId()) {
            $data['customerProfileId'] = $this->request->getCustomerProfileId();
            $data['customerPaymentProfileId'] = (string)$this->data->paymentProfile->customerPaymentProfileId;
            $cardRef = json_encode($data);
        }
        return $cardRef;
    }
}
