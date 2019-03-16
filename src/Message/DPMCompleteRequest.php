<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net DPM Complete Authorize Request
 */
class DPMCompleteRequest extends SIMCompleteRequest
{
    public function sendData($data)
    {
        return $this->response = new DPMCompleteResponse($this, $data);
    }
}
