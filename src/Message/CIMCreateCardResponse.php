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
        if (isset($this->data->customerPaymentProfileIdList)) {
            $data['customerPaymentProfileId'] = (string)$this->data->customerPaymentProfileIdList->numericString;
        }
        if (isset($this->data->customerShippingAddressIdList)) {
            $data['customerShippingAddressId'] = (string)$this->data->customerShippingAddressIdList->numericString;
        }

        if (!empty($data)) {
            $cardRef = json_encode($data);
        }

        return $cardRef;
    }
}
