<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Message\SIMAbstractRequest;

/**
 * Authorize.Net DPM Authorize Request.
 * Takes the data that will be used to create the direct-post form.
 */
class DPMAuthorizeRequest extends SIMAuthorizeRequest
{
    protected $action = 'AUTH_ONLY';

    public function getData()
    {
        $data = parent::getData();

        // If x_show_form is swet, then the form will be displayed on the Authorize.Net
        // gateway, which acts a bit like the SIM gateway. The documentation does NOT
        // make this clear.
        // TODO: revisit this - maybe much of what is in the DPM can be used to enhance
        // the SIM gateway, with very little in the DPM messages.

        //$data['x_show_form'] = 'PAYMENT_FORM';
        unset($data['x_show_form']);

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

        // The card details at this point are optional.
        if ($this->getCard()) {
            $data['x_card_num'] = $this->getCard()->getNumber();
            $data['x_exp_date'] = $this->getCard()->getExpiryDate('my');
            $data['x_card_code'] = $this->getCard()->getCvv();
        }

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
