<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net SIM Complete Authorize Response
 */

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SIMCompleteResponse extends AbstractResponse implements RedirectResponseInterface
{
    // Response codes returned by Authorize.Net

    const RESPONSE_CODE_APPROVED    = '1';
    const RESPONSE_CODE_DECLINED    = '2';
    const RESPONSE_CODE_ERROR       = '3';
    const RESPONSE_CODE_REVIEW      = '4';

    public function isSuccessful()
    {
        return static::RESPONSE_CODE_APPROVED === $this->getCode();
    }

    /**
     * If there is an error in the form, then the user should be able to go back
     * to the form and give it another shot.
     */
    public function isError()
    {
        return static::RESPONSE_CODE_ERROR === $this->getCode();
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

    /**
     * This message is handled in a notify, where a HTML redirect must be performed.
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * The merchant site notify handler needs to set the returnUrl in the complete request.
     */
    public function getRedirectUrl()
    {
        return $this->request->getReturnUrl();
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * There is no redirect data to send; the aim is just to get the user to a URL
     * by delivering a HTML page.
     */
    public function getRedirectData()
    {
        return array();
    }

    /**
     * Authorize.Net requires a redirect in a HTML page.
     * The OmniPay redirect helper will only provide a HTML page for the POST method
     * and then implements that through a self-submitting form, which will generate
     * browser warnings if returning to a non-SSL page. This JavScript and meta refresh
     * page avoids the security warning. No data is sent in this redirect, as that will
     * have all been saved with the transaction in storage.
     */
    public function getRedirectResponse()
    {
        $output = <<<ENDHTML
<!DOCTYPE html>
<html>
    <head>
        <title>Redirecting...</title>
        <meta http-equiv="refresh" content="0;url=%1\$s" />
    </head>
    <body>
        <p>Redirecting to <a href="%1\$s">payment complete page</a>...</p>
        <script type="text/javascript" charset="utf-8">
            window.location="%1\$s";
        </script>
    </body>
</html>
ENDHTML;

        $output = sprintf(
            $output,
            htmlentities($this->getRedirectUrl(), ENT_QUOTES, 'UTF-8', false)
        );

        return HttpResponse::create($output);
    }
}
