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

        $data = parent::getData();
        $data->transactionRequest->customerIP = $this->getClientIp();
        $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumber();
        $data->transactionRequest->payment->creditCard->expirationDate = $card->getExpiryDate('my');
        $data->transactionRequest->payment->creditCard->cardCode = $card->getCvv();

        $this->addBillingData($data);

        return $data;
    }
}
