<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM Abstract Request
 */
abstract class CIMAbstractRequest extends AIMAbstractRequest
{
    const VALIDATION_MODE_TEST = 'testMode';
    const VALIDATION_MODE_LIVE = 'liveMode';
    const VALIDATION_MODE_NONE = 'none';

    protected function addTransactionType(\SimpleXMLElement $data)
    {
        // Do nothing since customer profile requests have no transaction type
    }

    public function setValidationMode($value)
    {
        return $this->setParameter('validationMode', $value);
    }

    public function getValidationMode()
    {
        $validationMode = $this->getParameter('validationMode');

        if ($validationMode !== self::VALIDATION_MODE_NONE) {
            $validationMode = $this->getDeveloperMode() ? self::VALIDATION_MODE_TEST : self::VALIDATION_MODE_LIVE;
        }

        return $validationMode;
    }

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

    public function setCustomerProfileId($value)
    {
        return $this->setParameter('customerProfileId', $value);
    }

    public function getCustomerProfileId()
    {
        return $this->getParameter('customerProfileId');
    }

    public function setCustomerPaymentProfileId($value)
    {
        return $this->setParameter('customerPaymentProfileId', $value);
    }

    public function getCustomerPaymentProfileId()
    {
        return $this->getParameter('customerPaymentProfileId');
    }

    /**
     * Flag to force update consumer payment profile if duplicate is found
     *
     * @param $value
     *
     * @return $this
     */
    public function setForceCardUpdate($value)
    {
        return $this->setParameter('forceCardUpdate', $value);
    }

    public function getForceCardUpdate()
    {
        return $this->getParameter('forceCardUpdate');
    }

    public function setDefaultBillTo($defaultBillTo)
    {
        return $this->setParameter('defaultBillTo', $defaultBillTo);
    }

    public function getDefaultBillTo()
    {
        return $this->getParameter('defaultBillTo');
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');

        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->response = new CIMResponse($this, $httpResponse->getBody()->getContents());
    }
}
