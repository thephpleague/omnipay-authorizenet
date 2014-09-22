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
            // Update payment profile never returns customer profileId or payment profile Id. So use the request
            // data to generate the card reference
            $data['customerProfileId'] = $this->request->getCustomerProfileId();
            $data['customerPaymentProfileId'] = $this->request->getCustomerPaymentProfileId();

            $cardRef = json_encode($data);
        }
        return $cardRef;
    }
}
