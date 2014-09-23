<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Create card Response
 */
class CIMCreateCardResponse extends CIMAbstractResponse
{
    protected $xmlRootElement = 'createCustomerProfileResponse';

    public function getCardReference()
    {
        $cardRef = null;
        $data = array();
        if (isset($this->data->customerProfileId)) {
            // In case of a successful transaction, a "customerPaymentProfileId" element is present
            $data['customerProfileId'] = (string)$this->data->customerProfileId;
        }
        if (!empty($this->data->customerPaymentProfileIdList->numericString)) {
            $data['customerPaymentProfileId'] = (string)$this->data->customerPaymentProfileIdList->numericString;
        }

        if (!empty($data)) {
            $cardRef = json_encode($data);
        }

        return $cardRef;
    }
}
