<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Get payment profiles Response
 */
class CIMGetProfileResponse extends CIMCreatePaymentProfileResponse
{
    const ERROR_DUPLICATE_PROFILE = 'E00039';
    const ERROR_MAX_PAYMENT_PROFILE_LIMIT_REACHED = 'E00042';

    protected $responseType = 'getCustomerProfileResponse';

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

        // Handle quirkiness with XML -> JSON conversion
        if (!array_key_exists(0, $this->data['profile']['paymentProfiles'])) {
            $this->data['profile']['paymentProfiles'] = [$this->data['profile']['paymentProfiles']];
        }

        foreach ($this->data['profile']['paymentProfiles'] as $paymentProfile) {
            // For every payment  profile check if the last4 matches the last4 of the card in request.
            $cardLast4 = substr($paymentProfile['payment']['creditCard']['cardNumber'], -4);
            if ($last4 == $cardLast4) {
                return (string)$paymentProfile['customerPaymentProfileId'];
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
