<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Get payment profiles Response
 */
class CIMGetProfileResponse extends CIMCreateCardResponse
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

        foreach ($this->data->profile->paymentProfiles as $paymentProfile) {
            // For every payment  profile check if the last4 matches the last4 of the card in request.
            $cardLast4 = substr((string)$paymentProfile->payment->creditCard->cardNumber, -4);
            if ($last4 == $cardLast4) {
                return (string)$paymentProfile->customerPaymentProfileId;
            }
        }

        return null;
    }

    public function getCardReference()
    {
        $cardRef = null;
        if ($this->isSuccessful() && $this->request->getCustomerPaymentProfileId()) {
            $data['customerProfileId'] = $this->request->getCustomerProfileId();
            $data['customerPaymentProfileId'] = $this->request->getCustomerPaymentProfileId();
            $cardRef = json_encode($data);
        }
        return $cardRef;
    }
}
