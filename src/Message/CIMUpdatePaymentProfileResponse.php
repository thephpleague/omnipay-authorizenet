<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create payment profile Response
 */
class CIMUpdatePaymentProfileResponse extends CIMCreatePaymentProfileResponse
{
    protected $xmlRootElement = 'updateCustomerPaymentProfileResponse';

    public function getCardReference()
    {
        $cardRef = null;
        if ($this->isSuccessful()) {
            $data['customerProfileId'] = $this->request->getCustomerProfileId();
            $data['customerPaymentProfileId'] = $this->request->getCustomerPaymentProfileId();

            $cardRef = json_encode($data);
        }
        return $cardRef;
    }
}
