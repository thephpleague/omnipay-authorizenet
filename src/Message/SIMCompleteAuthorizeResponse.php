<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Authorize.Net SIM Complete Authorize Response
 */
class SIMCompleteAuthorizeResponse extends AbstractResponse
{
    // Response codes returned by Authorize.Net

    const RESPONSE_CODE_APPROVED    = '1';
    const RESPONSE_CODE_DECLINED    = '2';
    const RESPONSE_CODE_ERROR       = '3';
    const RESPONSE_CODE_REVIEW      = '4';

    public function isSuccessful()
    {
        return isset($this->data['x_response_code']) && static::RESPONSE_CODE_APPROVED === $this->data['x_response_code'];
    }

    public function getTransactionReference()
    {
        return isset($this->data['x_trans_id']) ? $this->data['x_trans_id'] : null;
    }

    public function getMessage()
    {
        return isset($this->data['x_response_reason_text']) ? $this->data['x_response_reason_text'] : null;
    }

    public function getReasonCode()
    {
        return isset($this->data['x_response_reason_code']) ? $this->data['x_response_reason_code'] : null;
    }

    public function getCode()
    {
        return isset($this->data['x_response_code']) ? $this->data['x_response_code'] : null;
    }
}
