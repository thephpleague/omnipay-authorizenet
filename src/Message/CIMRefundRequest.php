<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Creates a refund transaction request for the specified card, transaction
 */
class CIMRefundRequest extends CIMCaptureRequest
{
    protected $action = "profileTransRefund";

    protected $voidIfRefundFails = false;

    /**
     * @return boolean
     */
    public function isVoidIfRefundFails()
    {
        return $this->voidIfRefundFails;
    }

    /**
     * @param boolean $voidIfRefundFails
     */
    public function setVoidIfRefundFails($voidIfRefundFails)
    {
        $this->voidIfRefundFails = $voidIfRefundFails;
    }

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

    public function send()
    {
        /** @var CIMResponse $response */
        $response = parent::send();
        $parameters = $this->getParameters();

        if (!$response->isSuccessful() && $this->voidIfRefundFails &&
            $response->getResponseReasonCode() === CIMResponse::ERROR_RESPONSE_CODE_CANNOT_ISSUE_CREDIT) {
            // An attempt to a refund a transaction that was not settled. We can just void the entire transaction
            $voidRequest = new CIMVoidRequest($this->httpClient, $this->httpRequest);
            $voidRequest->initialize($parameters);
            $response = $voidRequest->send();
        }

        return $response;
    }
}
