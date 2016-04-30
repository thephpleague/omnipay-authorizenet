<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net SIM Authorize Request
 */
class SIMAuthorizeRequest extends SIMAbstractRequest
{
    protected $action = 'AUTH_ONLY';

    public function getData()
    {
        $this->validate('amount');

        // Either the nodifyUrl or the returnUrl can be provided.
        // The returnUrl is deprecated, as strictly this is a notifyUrl.
        if (!$this->getNotifyUrl()) {
            $this->validate('returnUrl');
        }

        $data = array();
        $data['x_login'] = $this->getApiLoginId();
        $data['x_type'] = $this->action;
        $data['x_version'] = '3.1';
        $data['x_method'] = 'CC';
        $data['x_fp_sequence'] = mt_rand();
        $data['x_fp_timestamp'] = time();
        $data['x_delim_data'] = 'FALSE';
        $data['x_show_form'] = 'PAYMENT_FORM';
        $data['x_relay_response'] = 'TRUE';

        if ($this->getClientIp()) {
            $data['x_customer_ip'] = $this->getClientIp();
        }

        // The returnUrl MUST be whitelisted in Authorize.net admin panel under
        // "Response/Receipt URLs".
        // Use the notifyUrl if available, as that is strictly what this is.
        // Fall back to returnUrl for BC support.
        $data['x_relay_url'] = $this->getNotifyUrl() ?: $this->getReturnUrl();
        $data['x_cancel_url'] = $this->getCancelUrl();

        if ($this->getCustomerId() !== null) {
            $data['x_cust_id'] = $this->getCustomerId();
        }

        if ($this->getCurrency() !== null) {
            $data['x_currency_code'] = $this->getCurrency();
        }

        if ($this->getTestMode()) {
            $data['x_test_request'] = 'TRUE';
        }

        $data = array_merge($data, $this->getBillingData());
        $data['x_fp_hash'] = $this->getHash($data);

        return $data;
    }

    /**
     * This hash is put into the form to confirm the amount has not been
     * modified en-route.
     * It uses the TransactionKey, which is a shared secret between the merchant
     * and Authorize.Net The sequence and timestamp provide additional salt.
     */
    public function getHash($data)
    {
        $fingerprint = implode(
            '^',
            array(
                $this->getApiLoginId(),
                $data['x_fp_sequence'],
                $data['x_fp_timestamp'],
                $data['x_amount']
            )
        ).'^';

        // If x_currency_code is specified, then it must follow the final trailing carat.
        if ($this->getCurrency()) {
            $fingerprint .= $this->getCurrency();
        }

        return hash_hmac('md5', $fingerprint, $this->getTransactionKey());
    }

    public function sendData($data)
    {
        return $this->response = new SIMAuthorizeResponse($this, $data, $this->getEndpoint());
    }
}
