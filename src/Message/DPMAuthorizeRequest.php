<?php

namespace Omnipay\AuthorizeNet\Message;

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

        // If x_show_form is set, then the form will be displayed on the Authorize.Net
        // gateway, in a similar way to the SIM gateway. The DPM documentation does NOT
        // make this clear at all.
        // Since x_show_form is set in the SIM gateway, make sure we unset it here.

        unset($data['x_show_form']);

        // Must be set for DPM.
        // This directs all errors to the relay response.

        $data['x_relay_always'] = 'TRUE';

        // The card details are optional.
        // They will most likely only be used for development and testing.
        // The card fields are still needed in the direct-post form regardless.

        if ($this->getCard()) {
            $data['x_card_num'] = $this->getCard()->getNumber();

            // Workaround for https://github.com/thephpleague/omnipay-common/issues/29
            $expiry_date = $this->getCard()->getExpiryDate('my');
            $data['x_exp_date'] = ($expiry_date === '1299' ? '' : $expiry_date);

            $data['x_card_code'] = $this->getCard()->getCvv();
        } else {
            $data['x_card_num'] = '';
            $data['x_exp_date'] = '';
            $data['x_card_code'] = '';
        }

        return $data;
    }


    /**
     * Given the DPM data, we want to turn it into a form for the user to submit to Authorize.net
     * The form may have most of the fields hidden, or may allow the user to change some details -
     * that depends on the use-case.
     * So this method will provide us with an object used to build the form.
     * @param $data
     * @return DPMResponse
     */
    public function sendData($data)
    {
        return $this->response = new DPMResponse($this, $data, $this->getEndpoint());
    }
}
