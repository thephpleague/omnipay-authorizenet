<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;
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

        $this->data = $this->xml2array($xml);
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
        $result = (string)$this->data['messages'][0]['resultCode'][0];
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

        if (isset($this->data['messages'])) {
            // In case of a successful transaction, a "messages" element is present
            $code = (string)$this->data['messages'][0]['message'][0]['code'][0];

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

        if (isset($this->data['messages'])) {
            // In case of a successful transaction, a "messages" element is present
            $message = (string)$this->data['messages'][0]['message'][0]['text'][0];

        }

        return $message;
    }

    public function getCardReference()
    {
        $cardRef = null;
        if ($this->isSuccessful()) {
            $data['customerProfileId'] = $this->getCustomerProfileId();
            $data['customerPaymentProfileId'] = $this->getCustomerPaymentProfileId();
            if (!empty($data['customerProfileId']) && !empty($data['customerPaymentProfileId'])) {
                // For card reference both profileId and payment profileId should exist
                $cardRef = json_encode($data);
            }
        }
        return $cardRef;
    }

    /**
     * http://bookofzeus.com/articles/convert-simplexml-object-into-php-array/
     *
     * Convert a simpleXMLElement in to an array
     *
     * @param \SimpleXMLElement $xml
     *
     * @return array
     */
    public function xml2array(\SimpleXMLElement $xml)
    {
        $arr = array();
        foreach ($xml as $element) {
            $tag = $element->getName();
            $e = get_object_vars($element);
            if (!empty($e)) {
                $arr[$tag][] = $element instanceof \SimpleXMLElement ? $this->xml2array($element) : $e;
            } else {
                $arr[$tag][] = trim($element);
            }
        }

        return $arr;
    }

    public function getCustomerProfileId()
    {
        return null;
    }

    public function getCustomerPaymentProfileId()
    {
        return null;
    }

    /**
     * Authorize net does not provide finger print and brand of the card hence we build the parameters from the
     * requested card data
     *
     */
    public function augmentResponse()
    {
        if ($this->isSuccessful()) {
            /** @var CreditCard $card */
            $card = $this->request->getCard();
            if ($card) {
                $ccString = $card->getNumber() . $card->getExpiryMonth() . $card->getExpiryYear();
                $this->data['hash'] = md5($ccString);
                $this->data['brand'] = $card->getBrand();
                $this->data['expiryYear'] = $card->getExpiryYear();
                $this->data['expiryMonth'] = $card->getExpiryMonth();
            }
        }
    }
}
