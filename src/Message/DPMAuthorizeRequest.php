<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Message\SIMAbstractRequest;

/**
 * Authorize.Net DPM Authorize Request
 */
class DPMAuthorizeRequest extends SIMAuthorizeRequest
{
    protected $action = 'AUTH_ONLY';

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
        // CHECKME: this may need to be back-ported to SIMAuthorizeRequest and AIMAuthorizeRequest
        // in order to supprot multiple currencies.

        if ($this->getCurrency()) {
            $fingerprint .= $this->getCurrency();
        }

        return hash_hmac('md5', $fingerprint, $this->getTransactionKey());
    }

    public function getData()
    {
        $data = parent::getData();

        // This is the DPM trigger.
        $data['x_show_form'] = 'PAYMENT_FORM';

        // Support multiple currencies.
        // CHECKME: should this be back-ported to SIMAuthorizeRequest and AIMAuthorizeRequest?

        if ($this->getCurrency()) {
            $data['x_currency_code'] = $this->getCurrency();
        }

        // CHECKME: x_recurring_billing is (ambiguously) listed as mandatory in the DPM docs.

        // The customer ID is optional.
        if ($this->getCustomerId()) {
            $data['x_cust_id'] = $this->getCustomerId();
        }

        $data['x_card_num'] = $this->getCard()->getNumber();
        $data['x_exp_date'] = $this->getCard()->getExpiryDate('my');
        $data['x_card_code'] = $this->getCard()->getCvv();

        return $data;
    }


    /**
     * Given the DPM data, we want to turn it into a form for the user to submit to Authorize.net
     * The form may have most of the fields hidden, or may allow the user to change some details -
     * that depends on the use-case.
     * So this method will provide us with an object used to build the form.
     */
    public function sendData($data)
    {
        return $this->response = new DPMAuthorizeResponse($this, $data, $this->getEndpoint());
    }
}
