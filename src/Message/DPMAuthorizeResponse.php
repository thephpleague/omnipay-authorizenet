<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Authorize.Net DPM Authorize Response
 * Here we want the application to present a POST form to the user. This object will
 * provide the helper methods for doing so.
 */
class DPMAuthorizeResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $postUrl;

    protected $hiddenFields = array(
        'x_fp_hash',
        'x_amount',
        'x_test_request',
        'x_cancel_url',
        'x_relay_url',
        'x_relay_response',
        'x_show_form',
        'x_delim_data',
        'x_fp_timestamp',
        'x_fp_sequence',
        'x_type',
        'x_login',
        'x_invoice_num',
        'x_description',
    );

    public function __construct(RequestInterface $request, $data, $postUrl)
    {
        $this->request = $request;
        $this->data = $data;
        $this->postUrl = $postUrl;
    }

    /**
     * Return false to indicate that more action is needed to complete
     * the transaction, a transparent redirect form in this case.
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * This is a transparent redirect transaction type, where a local form
     * will POST direct to the remote gateway.
     */
    public function isTransparentRedirect()
    {
        return true;
    }

    // Helpers to build the form.

    /**
     * The URL the form will be posted to.
     */
    public function getRedirectUrl()
    {
        return $this->postUrl;
    }

    public function getRedirectMethod()
    {
        return "post";
    }

    // CHECKME: do we still need getHiddenData()?
    public function getRedirectData()
    {
        return $this->getHiddenData();
    }

    /**
     * Add a field to the list of hidden fields.
     * The hidden fields are those we don't want to show the user, but
     * must still be posted.
     */
    public function setHiddenField($field_name)
    {
        if (!in_array($field_name, $this->hiddenFields)) {
            $this->hiddenFields[] = $field_name;
        }
    }

    /**
     * Remove a field from the list of hidden fields.
     */
    public function unsetHiddenField($field_name)
    {
        if (($key = array_search($field_name, $this->hiddenFields)) !== false) {
            unset($this->hiddenFields[$key]);
        }
    }

    /**
     * Data that must be included as hidden fields, if they are available at all.
     */
    public function getHiddenData()
    {
        return array_intersect_key($this->getData(), array_flip($this->hiddenFields));
    }

    /**
     * Data not in the hidden fields list.
     * These are not all mandatory, so you do not have to present all these
     * to the user.
     */
    public function getNonHiddenData()
    {
        return array_diff_key($this->getData(), array_flip($this->hiddenFields));
    }
}
