<?php

namespace Omnipay\AuthorizeNet\Model;

/**
 * Authorize.Net CIM allows storing credit cards within customer profiles. However, the card is not represented by a
 * single identifier, rather it requires a complex key to identify it. This class serves as a wrapper to help with
 * managing this complex key.
 *
 * @see http://developer.authorize.net/api/reference/features/customer_profiles.html
 */
class CardReference
{
    private $customerProfileId = null;
    private $paymentProfileId = null;
    private $shippingProfileId = null;

    /**
     * @param string $data JSON encoded string representing a card reference
     */
    public function __construct($data = null)
    {
        if ($data) {
            $data = json_decode($data);
            if (isset($data->customerProfileId)) {
                $this->customerProfileId = $data->customerProfileId;
            }
            if (isset($data->customerPaymentProfileId)) {
                $this->paymentProfileId = $data->customerPaymentProfileId;
            }
            if (isset($data->customerShippingAddressId)) {
                $this->shippingProfileId = $data->customerShippingAddressId;
            }
        }
    }

    public function __toString()
    {
        $data = array(
            'customerProfileId' => $this->customerProfileId,
            'customerPaymentProfileId' => $this->paymentProfileId
        );
        if ($this->shippingProfileId) {
            $data['customerShippingAddressId'] = $this->shippingProfileId;
        }
        return json_encode($data);
    }

    public function getCustomerProfileId()
    {
        return $this->customerProfileId;
    }

    public function setCustomerProfileId($customerProfileId)
    {
        $this->customerProfileId = $customerProfileId;
    }

    public function getPaymentProfileId()
    {
        return $this->paymentProfileId;
    }

    public function setPaymentProfileId($paymentProfileId)
    {
        $this->paymentProfileId = $paymentProfileId;
    }

    public function getShippingProfileId()
    {
        return $this->shippingProfileId;
    }

    public function setShippingProfileId($shippingProfileId)
    {
        $this->shippingProfileId = $shippingProfileId;
    }
}
