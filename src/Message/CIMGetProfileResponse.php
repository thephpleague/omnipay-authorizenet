<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Get payment profiles Response
 */
class CIMGetProfileResponse extends CIMAbstractResponse
{
    protected $xmlRootElement = 'getCustomerProfileResponse';

    public function getMatchingPaymentProfileId($last4)
    {
        if (!$this->isSuccessful()) {
            return null;
        }

//        $card = $this->request->getCard();
//        if ($card && $card->getNumber()) {
//            $last4 = substr($card->getNumber(), -4);

        foreach ($this->data->profile->paymentProfiles as $paymentProfile) {
            // For every payment  profile check if the last4 matches the last4 of the card in request.
            $cardLast4 = substr((string)$paymentProfile->payment->creditCard->cardNumber, -4);
            if ($last4 == $cardLast4) {
                return (string)$paymentProfile->customerPaymentProfileId;
            }
        }

        return null;
    }
}
