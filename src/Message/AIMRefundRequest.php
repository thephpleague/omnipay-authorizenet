<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.net AIM Refund Request
 */
class AIMRefundRequest extends AIMAbstractRequest
{
    protected $action = 'refundTransaction';

    public function shouldVoidIfRefundFails()
    {
        return !!$this->getParameter('voidIfRefundFails');
    }

    public function setVoidIfRefundFails($value)
    {
        $this->setParameter('voidIfRefundFails', $value);
    }

    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getParameter('amount');

        $transactionRef = $this->getTransactionReference();
        if ($card = $transactionRef->getCard()) {
            $data->transactionRequest->payment->creditCard->cardNumber = $card->number;
            $data->transactionRequest->payment->creditCard->expirationDate = $card->expiry;
        } elseif ($cardRef = $transactionRef->getCardReference()) {
            $data->transactionRequest->profile->customerProfileId = $cardRef->getCustomerProfileId();
            $data->transactionRequest->profile->paymentProfile->paymentProfileId = $cardRef->getPaymentProfileId();
        } else {
            // Transaction reference only contains the transaction ID, so a card is required
            $this->validate('card');
            $card = $this->getCard();
            $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumberLastFour();
            if ($card->getExpiryMonth()) {
                $data->transactionRequest->payment->creditCard->expirationDate = $card->getExpiryDate('my');
            }
        }
        $data->transactionRequest->refTransId = $transactionRef->getTransId();

        $this->addTransactionSettings($data);

        return $data;
    }

    public function send()
    {
        /** @var AIMResponse $response */
        $response = parent::send();

        if (!$response->isSuccessful() && $this->shouldVoidIfRefundFails() &&
            $response->getReasonCode() == AIMResponse::ERROR_RESPONSE_CODE_CANNOT_ISSUE_CREDIT
        ) {
            // This transaction has not yet been settled, hence cannot be refunded. But a void is possible.
            $voidRequest = new CIMVoidRequest($this->httpClient, $this->httpRequest);
            $voidRequest->initialize($this->getParameters());
            $response = $voidRequest->send();
        }

        return $response;
    }
}
