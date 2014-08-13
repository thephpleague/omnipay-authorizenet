<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest;
use Omnipay\AuthorizeNet\Message\AIMCaptureRequest;
use Omnipay\AuthorizeNet\Message\AIMPurchaseRequest;
use Omnipay\AuthorizeNet\Message\AIMVoidRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Authorize.Net AIM Class
 */
class AIMGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Authorize.Net AIM';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiLoginId' => '',
            'transactionKey' => '',
            'testMode' => false,
            'developerMode' => false,
        );
    }

    public function getApiLoginId()
    {
        return $this->getParameter('apiLoginId');
    }

    public function setApiLoginId($value)
    {
        return $this->setParameter('apiLoginId', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }

    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }

    public function setDeveloperMode($value)
    {
        return $this->setParameter('developerMode', $value);
    }

    /**
     * @param array $parameters
     * @return AIMAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMCaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMVoidRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMRefundRequest', $parameters);
    }

}
