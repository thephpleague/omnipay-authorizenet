<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMAuthorizeRequest extends AIMAbstractRequest
{
    protected $action = 'authOnlyTransaction';

    public function getData()
    {
        $this->validate('amount', 'card');

        /** @var CreditCard $card */
        $card = $this->getCard();
        $card->validate();

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumber();
        $data->transactionRequest->payment->creditCard->expirationDate = $card->getExpiryDate('my');
        $data->transactionRequest->payment->creditCard->cardCode = $card->getCvv();
        $ip = $this->getClientIp();
        if (!empty($ip)) {
            $data->transactionRequest->customerIP = $ip;
        }

        $this->addBillingData($data);
        $this->addTestModeSetting($data);
        $this->addExtraOptions($data);

        return $data;
    }
}
