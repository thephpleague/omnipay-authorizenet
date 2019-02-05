<?php

namespace Omnipay\AuthorizeNet;

/**
 * Authorize.Net DPM (Direct Post Method) Class
 */
class DPMGateway extends SIMGateway
{
    public function getName()
    {
        return 'Authorize.Net DPM';
    }

    /**
     * Helper to generate the authorize direct-post form.
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMAuthorizeRequest', $parameters);
    }

    /**
     * Get, validate, interpret and respond to the Authorize.Net callback.
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMCompleteRequest', $parameters);
    }

    /**
     * Helper to generate the purchase direct-post form.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMPurchaseRequest', $parameters);
    }

    /**
     * Get, validate, interpret and respond to the Authorize.Net callback.
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMCompleteRequest', $parameters);
    }
}
