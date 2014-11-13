<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\AuthorizeNet\Message\CIMCreateCardRequest;

/**
 * Authorize.Net CIM Class
 */
class CIMGateway extends AIMGateway
{
    public function getName()
    {
        return 'Authorize.Net CIM';
    }

    /**
     * @param array $parameters
     *
     * @return CIMCreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMCreateCardRequest', $parameters);
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMAuthorizeRequest', $parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMCaptureRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMPurchaseRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMRefundRequest', $parameters);
    }
}