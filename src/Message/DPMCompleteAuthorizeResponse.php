<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Authorize.Net DPM Complete Authorize Response
 * This is the result of handling the callback.
 * The result will always be a HTML redirect snippet. This gets
 * returned to the gateway, displayed in the user's browser, and a GET
 * redirect is performed using JavaScript and meta refresh (belt and braces).
 * We may want to return to the success page, the failed page or the retry
 * page (so the user can correct the form).
 */
class DPMCompleteAuthorizeResponse extends SIMCompleteAuthorizeResponse // TOOD: redirect
{
    const RESPONSE_CODE_APPROVED = '1';
    const RESPONSE_CODE_DECLINED = '2';
    const RESPONSE_CODE_ERROR = '3';
    const RESPONSE_CODE_REVIEW = '4';

    public function isSuccessful()
    {
        return isset($this->data['x_response_code']) && static::RESPONSE_CODE_APPROVED === $this->data['x_response_code'];
    }

    public function getTransactionReference()
    {
        return isset($this->data['x_trans_id']) ? $this->data['x_trans_id'] : null;
    }

    // TODO: getCode()?
    // TODO: redirect details (need "reptry" URL too).

    public function getMessage()
    {
        return isset($this->data['x_response_reason_text']) ? $this->data['x_response_reason_text'] : null;
    }
}
