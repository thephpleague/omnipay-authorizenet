<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Creates a Authorize only transaction request for the specified card
 */
class CIMAuthorizeRequest extends CIMAbstractRequest
{
    protected $xmlRootElement = 'createCustomerProfileTransactionRequest';

    protected $action = "profileTransAuthOnly";

    public function getData()
    {
        $this->validate('cardReference', 'amount');

        $data = $this->getBaseData();

        $this->addTransactionData($data);
        return $data;
    }

    /**
     * Adds transaction data
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addTransactionData(\SimpleXMLElement $data)
    {
        $transaction = $data->addChild('transaction');
        $action = $transaction->addChild($this->action);
        $action->amount = number_format($this->getAmount(), 2);

        $cardRef = json_decode($this->getCardReference(), true);
        $action->customerProfileId = $cardRef['customerProfileId'];
        $action->customerPaymentProfileId = $cardRef['customerPaymentProfileId'];
        if (!empty($cardRef['customerShippingAddressId'])) {
            $action->customerShippingAddressId = $cardRef['customerShippingAddressId'];
        }

        $desc = $this->getDescription();
        if (!empty($desc)) {
            $action->order->description = $desc;
        }

        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new CIMResponse($this, $httpResponse->getBody());
    }

}
