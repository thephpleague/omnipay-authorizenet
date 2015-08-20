<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net DPM Complete Authorize Request
 */
class DPMCompleteRequest extends SIMCompleteAuthorizeRequest
{
    public function sendData($data)
    {
        return $this->response = new DPMCompleteResponse($this, $data);
    }
}
