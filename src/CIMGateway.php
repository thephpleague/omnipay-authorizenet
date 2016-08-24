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

    public function setForceCardUpdate($forceCardUpdate)
    {
        return $this->setParameter('forceCardUpdate', $forceCardUpdate);
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

    /**
     * Create a new debit or credit card
     *
     * @param array $parameters
     *
     * @return CIMCreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMCreateCardRequest', $parameters);
    }

    public function getPaymentProfile(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMGetPaymentProfileRequest', $parameters);
    }

    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMDeletePaymentProfileRequest', $parameters);
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

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMVoidRequest', $parameters);
    }
}
