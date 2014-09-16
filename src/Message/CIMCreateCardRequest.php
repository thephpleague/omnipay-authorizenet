<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create Credit Card Request.
 */
class CIMCreateCardRequest extends CIMAbstractRequest
{
    public function getData()
    {
        $this->validate('card');

        /** @var CreditCard $card */
        $card = $this->getCard();
        $card->validate();

        $data = $this->getBaseData();

        $this->addBillingData($data);
        $this->addPaymentData($data);
        $this->addTestModeSetting($data);

        return $data;
    }

    /**
     * @throws InvalidRequestException
     * @return mixed|\SimpleXMLElement
     */
    public function getBaseData()
    {
        $data = new \SimpleXMLElement('<createCustomerProfileRequest/>');
        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');

        // Credentials
        $data->merchantAuthentication->name = $this->getApiLoginId();
        $data->merchantAuthentication->transactionKey = $this->getTransactionKey();

        return $data;
    }

    /**
     * Adds billing data to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addBillingData(\SimpleXMLElement $data)
    {
        // Merchant assigned customer ID
        $customer = $this->getCustomerId();
        if (!empty($customer)) {
            $data->profile->merchantCustomerId = $customer;
        }

        $description = $this->getDescription();
        if (!empty($description)) {
            $data->profile->description = $description;
        }

        $email = $this->getEmail();
        if (!empty($email)) {
            $data->profile->email = $email;
        }

        // This order is important. Payment profiles should come in this order only
        /** @var mixed $req */
        $data->profile->paymentProfiles = null;
        $req = $data->profile->paymentProfiles;

        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            // A card is present, so include billing details
            $req->billTo->firstName = $card->getBillingFirstName();
            $req->billTo->lastName = $card->getBillingLastName();
            $req->billTo->company = $card->getBillingCompany();
            $req->billTo->address = trim($card->getBillingAddress1() . " \n" . $card->getBillingAddress2());
            $req->billTo->city = $card->getBillingCity();
            $req->billTo->state = $card->getBillingState();
            $req->billTo->zip = $card->getBillingPostcode();
            $req->billTo->country = $card->getBillingCountry();
        }

        return $data;
    }

    /**
     * Adds payment data to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addPaymentData(\SimpleXMLElement $data)
    {
        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            // A card is present, so include payment details
            /** @var mixed $req */
            $data->profile->paymentProfiles->payment = null;
            $req = $data->profile->paymentProfiles->payment;

            $req->creditCard->cardNumber = $card->getNumber();
            $req->creditCard->expirationDate = $card->getExpiryDate('Y-m');
            $req->creditCard->cardCode = $card->getCvv();

            $req = $data->profile;
            $req->shipToList->firstName = $card->getShippingLastName();
            $req->shipToList->lastName = $card->getShippingLastName();
            $req->shipToList->company = $card->getShippingCompany();
            $req->shipToList->address = trim($card->getShippingAddress1() . " \n" . $card->getShippingAddress2());
            $req->shipToList->city = $card->getShippingCity();
            $req->shipToList->state = $card->getShippingState();
            $req->shipToList->zip = $card->getShippingPostcode();
            $req->shipToList->country = $card->getShippingCountry();
        }

        return $data;
    }

    protected function addTestModeSetting(\SimpleXMLElement $data)
    {
        // Test mode setting
        $data->validationMode = $this->getTestMode() ? 'testMode' : 'liveMode';

        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new CIMCreateCardResponse($this, $httpResponse->getBody());
    }

}
