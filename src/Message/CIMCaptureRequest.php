<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Creates a Authorize only transaction request for the specified card
 */
class CIMCaptureRequest extends CIMAuthorizeRequest
{
    protected $action = "profileTransCaptureOnly";

    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        // Get the card reference from the transaction reference and set it into the request. Card reference is required
        // to make all the transactions
        $transRef = json_decode($this->getTransactionReference(), true);
        $this->setCardReference($transRef['cardReference']);

        $data = $this->getBaseData();

        $this->addTransactionData($data);
        $this->addTransactionReferenceData($data);
        return $data;
    }

    /**
     * Adds references for original transaction to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addTransactionReferenceData(\SimpleXMLElement $data)
    {
        $action = $data->transaction->profileTransCaptureOnly;

        $transRef = json_decode($this->getTransactionReference(), true);

        $action->approvalCode = $transRef['approvalCode'];
        return $data;
    }
}
