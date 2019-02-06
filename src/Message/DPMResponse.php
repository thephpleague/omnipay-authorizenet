<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Authorize.Net DPM Authorize and Purchase Response
 * We want the application to present a POST form to the user. This object will
 * provide the helper methods for doing so.
 */
class DPMResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $postUrl;

    /**
     * These will be hidden fields in the direct-post form.
     * Not all are required
     */
    protected $hiddenFields = array(
        // Merchant
        'x_login',

        // Fingerprint
        'x_fp_hash',
        'x_fp_sequence',
        'x_fp_timestamp',

        // Transaction
        'x_type',
        'x_version',
        'x_method',

        // Payment
        'x_amount',
        'x_currency_code',
        'x_tax',
        'x_freight',
        'x_duty',
        'x_tax_exempt',

        // Relay response
        'x_relay_response',
        'x_relay_url',
        'x_relay_always',
        'x_cancel_url',

        // AFDS
        'x_customer_ip',

        // Testing
        'x_test_request',

        'x_invoice_num',
        'x_description',
        'x_cust_id',
        'x_email_customer',

        'x_delim_data',

        // Custom omnipay field.
        'omnipay_transaction_id',
    );

    /**
     * Maps OmniPay field names to Authorize.Net field names.
     */
    protected $fieldMapping = array(
    );

    public function __construct(RequestInterface $request, $data, $postUrl)
    {
        parent::__construct($request, $data);

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
        return 'POST';
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
     * @param $field_name
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
