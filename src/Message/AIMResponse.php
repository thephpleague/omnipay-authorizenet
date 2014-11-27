<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Authorize.Net AIM Response
 */
class AIMResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;

        // Strip out the xmlns junk so that PHP can parse the XML
        $xml = preg_replace('/<createTransactionResponse[^>]+>/', '<createTransactionResponse>', (string)$data);

        try {
            $xml = simplexml_load_string($xml);
        } catch (\Exception $e) {
            throw new InvalidResponseException();
        }

        if (!$xml) {
            throw new InvalidResponseException();
        }

        $this->data = $xml;
    }

    public function isSuccessful()
    {
        return 1 === $this->getResultCode();
    }

    /**
     * Overall status of the transaction. This field is also known as "Response Code" in Authorize.NET terminology.
     *
     * @return int 1 = Approved, 2 = Declined, 3 = Error, 4 = Held for Review
     */
    public function getResultCode()
    {
        return intval((string)$this->data->transactionResponse[0]->responseCode);
    }

    /**
     * A more detailed version of the Result/Response code.
     *
     * @return int|null
     */
    public function getReasonCode()
    {
        $code = null;

        if (isset($this->data->transactionResponse[0]->messages)) {
            // In case of a successful transaction, a "messages" element is present
            $code = intval((string)$this->data->transactionResponse[0]->messages[0]->message[0]->code);

        } elseif (isset($this->data->transactionResponse[0]->errors)) {
            // In case of an unsuccessful transaction, an "errors" element is present
            $code = intval((string)$this->data->transactionResponse[0]->errors[0]->error[0]->errorCode);
        }

        return $code;
    }

    /**
     * Text description of the status.
     *
     * @return string|null
     */
    public function getMessage()
    {
        $message = null;

        if (isset($this->data->transactionResponse[0]->messages)) {
            // In case of a successful transaction, a "messages" element is present
            $message = (string)$this->data->transactionResponse[0]->messages[0]->message[0]->description;

        } elseif (isset($this->data->transactionResponse[0]->errors)) {
            // In case of an unsuccessful transaction, an "errors" element is present
            $message = (string)$this->data->transactionResponse[0]->errors[0]->error[0]->errorText;
        }

        return $message;
    }

    public function getAuthorizationCode()
    {
        return (string)$this->data->transactionResponse[0]->authCode;
    }

    /**
     * Returns the Address Verification Service return code.
     *
     * @return string A single character. Can be A, B, E, G, N, P, R, S, U, X, Y, or Z.
     */
    public function getAVSCode()
    {
        return (string)$this->data->transactionResponse[0]->avsResultCode;
    }

    /**
     * The payment gateway assigned identification number for transaction.
     *
     * @return string
     */
    public function getTransactionReference()
    {
        return (string)$this->data->transactionResponse[0]->transId;
    }
}
