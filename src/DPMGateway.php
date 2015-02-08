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

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMAuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMCompleteRequest', $parameters);
    }

    public function payment(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMPaymentRequest', $parameters);
    }

    public function completePayment(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMCompleteRequest', $parameters);
    }
}
