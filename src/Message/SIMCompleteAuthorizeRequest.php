<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net SIM Complete Authorize Request
 */
class SIMCompleteAuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        if (strtolower($this->httpRequest->request->get('x_MD5_Hash')) !== $this->getHash()) {
            throw new InvalidRequestException('Incorrect hash');
        }

        return $this->httpRequest->request->all();
    }

    /**
     * CHECKME: should this be the transactionReference in the hash, not the transactionId?
     * The transaction reference and the amount are both sent by the remote gateway (x_trans_id
     * and x_amount) and it is those that should be checked against.
     */
    public function getHash()
    {
        return md5($this->getHashSecret().$this->getApiLoginId().$this->getTransactionId().$this->getAmount());
    }

    public function sendData($data)
    {
        return $this->response = new SIMCompleteAuthorizeResponse($this, $data);
    }
}
