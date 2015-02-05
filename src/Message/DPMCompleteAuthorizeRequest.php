<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net DPM Complete Authorize Request
 */
class DPMCompleteAuthorizeRequest extends SIMCompleteAuthorizeRequest
{
    public function getData()
    {
        $hash_posted = strtolower($this->httpRequest->request->get('x_MD5_Hash'));
        $posted_transaction_reference = $this->httpRequest->request->get('x_trans_id');
        $posted_amount = $this->httpRequest->request->get('x_amount');
        $hash_calculated = $this->getDpmHash($posted_transaction_reference, $posted_amount);

        if ($hash_posted !== $hash_calculated) {
            // If the hash is incorrect, then we can't trust the source nor anything sent.

            throw new InvalidRequestException('Incorrect hash');
        }

        // The hashes have passed, but the amount should also be validated against the
        // amount in the stored and retrieved transaction. If the application has the
        // ability to retrieve the transaction (using the transaction_id sent as a custom
        // form field, or perhaps as a GET parameter on the callback URL) then it will
        // be checked here.

        $amount = $this->getAmount();

        if (isset($amount) && $amount != $posted_amount) {
            // The amounts don't match up. Someone may have been playing with the
            // transaction references.

            throw new InvalidRequestException('Incorrect amount');
        }

        return $this->httpRequest->request->all();
    }

    /**
     * This hash confirms the ransaction has come from the Authorize.Net gateway.
     * It basically tests the shared hash secret is correct, but mixes in other details
     * that will change for each transaction so the hash will be unique for each transaction.
     * The hash secret and login ID are known to the merchent site, and the amount and transaction
     * reference (x_amount and x_trans_id) are sent by the gatewa.
     */
    public function getDpmHash($transaction_reference, $amount)
    {
        return md5(
            $this->getHashSecret()
            . $this->getApiLoginId()
            . $transaction_reference
            . $amount
        );
    }

    public function sendData($data)
    {
        return $this->response = new DPMCompleteAuthorizeResponse($this, $data);
    }
}
