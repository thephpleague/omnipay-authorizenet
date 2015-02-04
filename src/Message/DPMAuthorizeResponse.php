<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Authorize.Net DPM Authorize Response
 * Here we want the application to present a POST form to the user. This object will
 * provide the helper methods for doing so.
 */
class DPMAuthorizeResponse extends AbstractResponse
{
    protected $postUrl;

    public function __construct(RequestInterface $request, $data, $postUrl)
    {
        $this->request = $request;
        $this->data = $data;
        $this->postUrl = $postUrl;
    }

    public function isSuccessful()
    {
        return true;
    }

    public function getPostUrl()
    {
        return $this->postUrl;
    }

    // Testing: use getData() to look at what we have.
}
