<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net SIM Complete Authorize Request
 */
class SIMCompleteRequest extends SIMAbstractRequest
{
    /**
     * Get the transaction ID passed in through the custom field.
     * This is used to look up the transaction in storage.
     */
    public function getTransactionId()
    {
        return $this->httpRequest->request->get(static::TRANSACTION_ID_PARAM);
    }

    public function getData()
    {
        // The hash sent in the callback from the Authorize.Net gateway.
        $hashPosted = $this->getPostedHash();

        // Calculate the hash locally, using the shared "hash secret" and login ID.
        $hashCalculated = $this->getHash();

        if ($hashPosted !== $hashCalculated) {
            // If the hash is incorrect, then we can't trust the source nor anything sent.
            // Throwing exceptions here is probably a bad idea. We are trying to get the data,
            // and if it is invalid, then we need to be able to log that data for analysis.
            // Except we can't, baceuse the exception means we can't get to the data.
            // For now, this is consistent with other OmniPay gateway drivers.

            throw new InvalidRequestException('Incorrect hash');
        }

        // The hashes have passed, but the amount should also be validated against the
        // amount in the stored and retrieved transaction. If the application has the
        // ability to retrieve the transaction (using the transaction_id sent as a custom
        // form field, or perhaps in an otherwise unused field such as x_invoice_id.

        $amount = $this->getAmount();
        $postedAmount = $this->httpRequest->request->get('x_amount');

        if (isset($amount) && $amount != $postedAmount) {
            // The amounts don't match. Someone may have been playing with the
            // transaction references.

            throw new InvalidRequestException('Incorrect amount');
        }

        return $this->httpRequest->request->all();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        if ($this->getSignatureKey()) {
            return $this->getSha512Hash();
        } else {
            return $this->getMd5Hash();
        }
    }

    /**
     * Generate md5 hash.
     *
     * @param $transaction_reference
     * @param $amount
     * @return string
     */
    public function getMd5Hash()
    {
        $transactionReference = $this->httpRequest->request->get('x_trans_id');
        $amount = $this->httpRequest->request->get('x_amount');

        $key = array(
            $this->getHashSecret(),
            $this->getApiLoginId(),
            $transactionReference,
            $amount,
        );

        return md5(implode('', $key));
    }

    /**
     * Generate sha512 hash.
     * Required fields are provided in Table 18 in
     * https://www.authorize.net/content/dam/authorize/documents/SIM_guide.pdf#page=73
     *
     * @return string hash generated from server request transformed to upper case
     */
    public function getSha512Hash()
    {
        $signatureKey = $this->getSignatureKey();
        $request = $this->httpRequest->request;

        $hashData = '^' . implode('^', [
            $request->get('x_trans_id'),
            $request->get('x_test_request'),
            $request->get('x_response_code'),
            $request->get('x_auth_code'),
            $request->get('x_cvv2_resp_code'),
            $request->get('x_cavv_response'),
            $request->get('x_avs_code'),
            $request->get('x_method'),
            $request->get('x_account_number'),
            $request->get('x_amount'),
            $request->get('x_company'),
            $request->get('x_first_name'),
            $request->get('x_last_name'),
            $request->get('x_address'),
            $request->get('x_city'),
            $request->get('x_state'),
            $request->get('x_zip'),
            $request->get('x_country'),
            $request->get('x_phone'),
            $request->get('x_fax'),
            $request->get('x_email'),
            $request->get('x_ship_to_company'),
            $request->get('x_ship_to_first_name'),
            $request->get('x_ship_to_last_name'),
            $request->get('x_ship_to_address'),
            $request->get('x_ship_to_city'),
            $request->get('x_ship_to_state'),
            $request->get('x_ship_to_zip'),
            $request->get('x_ship_to_country'),
            $request->get('x_invoice_num'),
        ]) . '^';
        $hash = hash_hmac('sha512', $hashData, hex2bin($signatureKey));

        return strtoupper($hash);
    }

    /**
     * Get posted hash from the callback from the Authorize.Net gateway.
     *
     * @return string|null
     */
    public function getPostedHash()
    {
        if ($signatureKey = $this->getSignatureKey()) {
            return strtoupper($this->httpRequest->request->get('x_SHA2_Hash'));
        }

        return strtolower($this->httpRequest->request->get('x_MD5_Hash'));
    }

    public function sendData($data)
    {
        return $this->response = new SIMCompleteResponse($this, $data);
    }
}
