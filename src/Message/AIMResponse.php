<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Authorize.Net AIM Response
 */
class AIMResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $rawFields = substr($data, 1, - 1);
        if ($rawFields !== false) {
            $temp = explode('|,|', $rawFields);
        } else {
            $temp = array();
        }

        $response_fields = array(
            'Response Code',
            'Response Subcode',
            'Response Reason Code',
            'Response Reason Text',
            'Authorization Code',
            'AVS Response',
            'Transaction ID',
            'Invoice Number',
            'Description',
            'Amount',
            'Method',
            'Transaction Type',
            'Customer ID',
            'First Name',
            'Last Name',
            'Company',
            'Address',
            'City',
            'State',
            'ZIP Code',
            'Country',
            'Phone',
            'Fax',
            'Email Address',
            'Ship To First Name',
            'Ship To Last Name',
            'Ship To Company',
            'Ship To Address',
            'Ship To City',
            'Ship To State',
            'Ship To ZIP Code',
            'Ship To Country',
            'Tax',
            'Duty',
            'Freight',
            'Tax Exempt',
            'Purchase Order Number',
            'MD5 Hash',
            'Card Code Response',
            'Cardholder Authentication Verification Response',
            'Account Number',
            'Card Type',
            'Split Tender ID',
            'Requested Amount',
            'Balance On Card'
        );

        $response = array();

        foreach ($response_fields as $field) {
            $responseField = array_shift($temp);
            if (!is_null($responseField)) {
                $response[$field] = $responseField;
            }
        }

        $response_codes = array(
            1 => 'Approved',
            2 => 'Declined',
            3 => 'Error',
            4 => 'Held for Review'
        );

        $avs_response_codes = array(
            'A' => 'Address (Street) matches, ZIP does not',
            'B' => 'Address information not provided for AVS check',
            'E' => 'AVS error',
            'G' => 'Non-U.S. Card Issuing Bank',
            'N' => 'No Match on Address (Street) or ZIP',
            'P' => 'AVS not applicable for this transaction',
            'R' => 'Retry?System unavailable or timed out',
            'S' => 'Service not supported by issuer',
            'U' => 'Address information is unavailable',
            'W' => 'Nine digit ZIP matches, Address (Street) does not',
            'X' => 'Address (Street) and nine digit ZIP match',
            'Y' => 'Address (Street) and five digit ZIP match',
            'Z' => 'Five digit ZIP matches, Address (Street) does not'
        );

        if (isset($response['Response Code']) && isset($response_codes[$response['Response Code']])) {
            $response['Response Code Message'] = $response_codes[$response['Response Code']];
        } else {
            $response['Response Code Message'] = null;
        }

        if (isset($response['AVS Response']) && isset($avs_response_codes[$response['AVS Response']])) {
            $response['AVS Response Message'] = $avs_response_codes[$response['AVS Response']];
        } else {
            $response['AVS Response Message'] = null;
        }

        $this->data = $response;

        if (count($this->data) < 10) {
            throw new InvalidResponseException();
        }
    }

    public function isSuccessful()
    {
        return $this->getCodeMessage() == 'Approved';
    }

    public function getCode()
    {
        return $this->data['Response Code'];
    }

    public function getCodeMessage()
    {
        return $this->data['Response Code Message'];
    }

    public function getReasonCode()
    {
        return $this->data['Response Reason Code'];
    }

    public function getMessage()
    {
        return $this->data['Response Reason Text'];
    }

    public function getAuthorizationCode()
    {
        return $this->data['Authorization Code'];
    }

    public function getAVSCode()
    {
        return $this->data['AVS Response'];
    }

    public function getAVSCodeMessage()
    {
        return $this->data['AVS Response Message'];
    }

    public function getTransactionReference()
    {
        return $this->data['Transaction ID'];
    }
}
