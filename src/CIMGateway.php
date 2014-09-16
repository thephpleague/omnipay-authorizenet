<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Common\AbstractGateway;

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
     * @return AIMAuthorizeRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CIMCreateCardRequest', $parameters);
    }

}