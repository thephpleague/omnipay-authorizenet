<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net CIM Abstract Request
 */
abstract class CIMAbstractRequest extends AIMAbstractRequest
{
    protected $xmlRootElement = null;

    // Need the below setters and getters for accessing this data within createCardRequest.send
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    public function getName()
    {
        return $this->getParameter('name');
    }

    /**
     * @throws InvalidRequestException
     * @return mixed|\SimpleXMLElement
     */
    public function getBaseData()
    {
        $data = new \SimpleXMLElement("<" . $this->xmlRootElement . "/>");
        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');

        // Credentials
        $data->merchantAuthentication->name = $this->getApiLoginId();
        $data->merchantAuthentication->transactionKey = $this->getTransactionKey();

        return $data;
    }
}
