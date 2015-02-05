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

/*    public function setHashSecret()
    {
    } */

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMAuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\DPMCompleteAuthorizeRequest', $parameters);
    }
}
