<?php

namespace Omnipay\AuthorizeNet;

/**
 * Authorize.Net SIM Class
 */
class SIMGateway extends AIMGateway
{
    public function getName()
    {
        return 'Authorize.Net SIM';
    }

    public function getDefaultParameters()
    {
        $parameters = parent::getDefaultParameters();
        $parameters = array_merge($parameters, array(
            'signatureKey' => null,
            'liveEndpoint' => 'https://secure2.authorize.net/gateway/transact.dll',
            'developerEndpoint' => 'https://test.authorize.net/gateway/transact.dll'
        ));
        return $parameters;
    }

    public function getApiLoginId()
    {
        return $this->getParameter('apiLoginId');
    }

    public function setApiLoginId($value)
    {
        return $this->setParameter('apiLoginId', $value);
    }

    public function getSignatureKey()
    {
        return $this->getParameter('signatureKey');
    }

    public function setSignatureKey($value)
    {
        return $this->setParameter('signatureKey', $value);
    }

    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }

    public function setDeveloperMode($value)
    {
        return $this->setParameter('developerMode', $value);
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\SIMAuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\SIMCompleteRequest', $parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\SIMCaptureRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\SIMPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->completeAuthorize($parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\SIMVoidRequest', $parameters);
    }
}
