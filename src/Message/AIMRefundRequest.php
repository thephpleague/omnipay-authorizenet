<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.net AIM Refund Request
 */
class AIMRefundRequest extends AIMAbstractRequest
{
    protected $action = 'refundTransaction';

    public function getData()
    {
        $this->validate('transactionReference', 'amount', 'card');

        /** @var CreditCard $card */
        $card = $this->getCard();

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getParameter('amount');
        $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumber();
        $data->transactionRequest->payment->creditCard->expirationDate = $card->getExpiryDate('my');
        $data->transactionRequest->refTransId = $this->getTransactionReference();
        $this->addTestModeSetting($data);

        return $data;
    }
}
