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

    /**
     * These will be hidden fields in the direct-post form.
     */
    protected $hiddenFields = array(
        'x_fp_hash',
        'x_amount',
        'x_currency_code',
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
        'x_cust_id',
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

    public function isRedirect()
    {
        return true;
    }

    // Helpers to build the form.

    /**
     * The URL the form will POST to.
     */
    public function getRedirectUrl()
    {
        return $this->postUrl;
    }

    public function getRedirectMethod()
    {
        return "post";
    }

    /**
     * Data that must be included as hidden fields.
     */
    public function getRedirectData()
    {
        return array_intersect_key($this->getData(), array_flip($this->hiddenFields));
    }

    /**
     * Move a field to the list of hidden form fields.
     * The hidden fields are those we don't want to show the user, but
     * must still be posted.
     */
    public function hideField($field_name)
    {
        if (!in_array($field_name, $this->hiddenFields)) {
            $this->hiddenFields[] = $field_name;
        }
    }

    /**
     * Remove a field from the list of hidden fields.
     */
    public function unhideField($field_name)
    {
        if (($key = array_search($field_name, $this->hiddenFields)) !== false) {
            unset($this->hiddenFields[$key]);
        }
    }

    /**
     * Data not in the hidden fields list.
     * These are not all mandatory, so you do not have to present all these
     * to the user. You may also have custom fields you want to post, such
     * as the merchant transactionId (if not using invoiceId for this purpose).
     */
    public function getVisibleData()
    {
        return array_diff_key($this->getData(), array_flip($this->hiddenFields));
    }
}
