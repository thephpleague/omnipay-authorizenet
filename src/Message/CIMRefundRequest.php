<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Creates a refund transaction request for the specified card, transaction
 */
class CIMRefundRequest extends CIMCaptureRequest
{
    protected $action = "profileTransRefund";

    /**
     * Adds reference for original transaction to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addTransactionReferenceData(\SimpleXMLElement $data)
    {
        $action = $data->transaction->profileTransRefund;

        $transRef = json_decode($this->getTransactionReference(), true);

        $action->transId = $transRef['transId'];
        return $data;
    }
}
