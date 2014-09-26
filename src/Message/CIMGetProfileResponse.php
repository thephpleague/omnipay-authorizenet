<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Get payment profiles Response
 */
class CIMGetProfileResponse extends CIMCreatePaymentProfileResponse
{
    protected $xmlRootElement = 'getCustomerProfileResponse';

    /**
     * Get the payment profile id corresponding to the specified last4 by looking into the payment profiles
     * of the customer
     *
     * @param $last4
     *
     * @return null|string
     */
    public function getMatchingPaymentProfileId($last4)
    {
        if (!$this->isSuccessful()) {
            return null;
        }

        foreach ($this->data['profile'][0]['paymentProfiles'] as $paymentProfile) {
            // For every payment  profile check if the last4 matches the last4 of the card in request.
            $cardLast4 = substr($paymentProfile['payment'][0]['creditCard'][0]['cardNumber'][0], -4);
            if ($last4 == $cardLast4) {
                return (string)$paymentProfile['customerPaymentProfileId'][0];
            }
        }

        return null;
    }

    public function getCustomerPaymentProfileId()
    {
        if ($this->isSuccessful()) {
            return $this->request->getCustomerPaymentProfileId();
        }
        return null;
    }
}
