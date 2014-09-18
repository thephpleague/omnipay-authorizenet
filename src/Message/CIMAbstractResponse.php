<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Authorize.Net CIM Response
 */
class CIMAbstractResponse extends AbstractResponse
{
    protected $xmlRootElement = null;

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;

        // Check if this is an error response
        $isError = strpos((string)$data, '<ErrorResponse');

        $xmlRootElement = $isError !== false ? 'ErrorResponse' : $this->xmlRootElement;
        // Strip out the xmlns junk so that PHP can parse the XML
        $xml = preg_replace('/<' . $xmlRootElement . '[^>]+>/', '<' . $xmlRootElement . '>', (string)$data);

        try {
            $xml = simplexml_load_string($xml);
        } catch(\Exception $e) {
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
        $result = (string)$this->data->messages[0]->resultCode;
        switch ($result) {
            case 'Ok':
                return 1;
            case 'Error':
                return 3;
            default:
                return null;

        }
    }

    /**
     * A more detailed version of the Result/Response code.
     *
     * @return int|null
     */
    public function getReasonCode()
    {
        $code = null;

        if (isset($this->data->messages)) {
            // In case of a successful transaction, a "messages" element is present
            $code = (string)$this->data->messages[0]->message[0]->code;

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

        if (isset($this->data->messages)) {
            // In case of a successful transaction, a "messages" element is present
            $message = (string)$this->data->messages[0]->message[0]->text;

        }

        return $message;
    }

    public function getCardReference()
    {
        return null;
    }
}
