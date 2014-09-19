<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create payment profile Response
 */
class CIMCreatePaymentProfileResponse extends CIMCreateCardResponse
{
    protected $xmlRootElement = 'createCustomerPaymentProfileResponse';

    public function getCardReference()
    {
        $cardRef = null;
        if ($this->isSuccessful()) {
            $data['customerProfileId'] = $this->request->getCustomerProfileId();
            if (isset($this->data->customerPaymentProfileId)) {
                $data['customerPaymentProfileId'] = (string)$this->data->customerPaymentProfileId;
            }

            $cardRef = json_encode($data);
        }
        return $cardRef;
    }
}
