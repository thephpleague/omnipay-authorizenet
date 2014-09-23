<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Create Credit Card Request.
 */
class CIMCreateCardRequest extends CIMAbstractRequest
{
    protected $xmlRootElement = 'createCustomerProfileRequest';

    public function getData()
    {
        $this->validate('card');

        /** @var CreditCard $card */
        $card = $this->getCard();
        $card->validate();

        $data = $this->getBaseData();
        $this->addProfileData($data);
        $this->addTestModeSetting($data);

        return $data;
    }

    /**
     * Add customer profile data to the specified xml element
     *
     * @param \SimpleXMLElement $data
     */
    protected function addProfileData(\SimpleXMLElement $data)
    {
        $req = $data->addChild('profile');
        // Merchant assigned customer ID
        $customer = $this->getCustomerId();
        if (!empty($customer)) {
            $req->merchantCustomerId = $customer;
        }

        $description = $this->getDescription();
        if (!empty($description)) {
            $req->description = $description;
        }

        $email = $this->getEmail();
        if (!empty($email)) {
            $req->email = $email;
        }

        $this->addPaymentProfileData($req);
        $this->addShippingData($req);
    }

    /**
     * Adds payment profile to the specified xml element
     *
     * @param \SimpleXMLElement $data
     */
    protected function addPaymentProfileData(\SimpleXMLElement $data)
    {
        // This order is important. Payment profiles should come in this order only
        $req = $data->addChild('paymentProfiles');
        $this->addBillingData($req);
    }

    /**
     * Adds billing/payment data to the specified xml element
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement|void
     */
    protected function addBillingData(\SimpleXMLElement $data)
    {
        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            $req = $data->addChild('billTo');
            // A card is present, so include billing details
            $req->firstName = $card->getBillingFirstName();
            $req->lastName = $card->getBillingLastName();
            $req->company = $card->getBillingCompany();
            $req->address = trim($card->getBillingAddress1() . " \n" . $card->getBillingAddress2());
            $req->city = $card->getBillingCity();
            $req->state = $card->getBillingState();
            $req->zip = $card->getBillingPostcode();
            $req->country = $card->getBillingCountry();

            $req = $data->addChild('payment');
            $req->creditCard->cardNumber = $card->getNumber();
            $req->creditCard->expirationDate = $card->getExpiryDate('Y-m');
            $req->creditCard->cardCode = $card->getCvv();
        }
    }

    /**
     * Adds shipping data to the specified xml element
     *
     * @param \SimpleXMLElement $data
     */
    protected function addShippingData(\SimpleXMLElement $data)
    {
        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            if ($card->getShippingFirstName()) {
                $data->shipToList->firstName = $card->getShippingFirstName();
            }
            if ($card->getShippingLastName()) {
                $data->shipToList->lastName = $card->getShippingLastName();
            }
            if ($card->getShippingCompany()) {
                $data->shipToList->company = $card->getShippingCompany();
            }
            if ($card->getShippingAddress1() || $card->getShippingAddress2()) {
                $data->shipToList->address = trim($card->getShippingAddress1() . " \n" . $card->getShippingAddress2());
            }
            if ($card->getShippingCity()) {
                $data->shipToList->city = $card->getShippingCity();
            }
            if ($card->getShippingState()) {
                $data->shipToList->state = $card->getShippingState();
            }
            if ($card->getShippingPostcode()) {
                $data->shipToList->zip = $card->getShippingPostcode();
            }
            if ($card->getShippingCountry()) {
                $data->shipToList->country = $card->getShippingCountry();
            }
        }
    }

    protected function addTestModeSetting(\SimpleXMLElement $data)
    {
        // Test mode setting
        $data->validationMode = $this->getDeveloperMode() ? 'testMode' : 'liveMode';
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        $response = new CIMCreateCardResponse($this, $httpResponse->getBody());

        if (!$response->isSuccessful() && $response->getReasonCode() == 'E00039') {
            // Duplicate profile. Try adding a new payment profile for the same profile and get the response
            $response = $this->createPaymentProfile($response);

        }

        return $this->response = $response;
    }

    /**
     * Attempts to add a payment profile to the existing customer profile.
     *
     * @param CIMCreateCardResponse $response Duplicate customer profile response
     *
     * @return CIMCreateCardResponse
     */
    public function createPaymentProfile(CIMCreateCardResponse $response)
    {
        // Parse the customer profile Id from the message
        $msg = $response->getMessage();
        preg_match("/ID (.+) already/i", $msg, $matches);
        if (empty($matches[1])) {
            // Duplicate profile id not found. Return current response
            return $response;
        }

        // Use the customerProfileId and create a payment profile for the customer
        $parameters = array_replace($this->getParameters(), array('customerProfileId' => $matches[1]));
        $obj = new CIMCreatePaymentProfileRequest($this->httpClient, $this->httpRequest);
        $obj->initialize($parameters);
        $paymentProfileResponse = $obj->send();
        if (!$paymentProfileResponse->isSuccessful() &&
            $paymentProfileResponse->getReasonCode() == 'E00039' && $this->getForceCardUpdate() == true
        ) {
            // Found a duplicate payment profile existing for the same card data. Force update is turned on,
            // so get the complete profile of the user and find the payment profile id matching the credit card number
            // and update the payment profile with the card details.
            $card = $this->getCard();
            $last4 = substr($card->getNumber(), -4);
            $getProfileResponse = $this->getProfile($parameters);
            $customerPaymentProfileId = $getProfileResponse->getMatchingPaymentProfileId($last4);
            if (!$customerPaymentProfileId) {
                // Matching customer payment profile id not found. Return the original response
                return $response;
            }

            $parameters['customerPaymentProfileId'] = $customerPaymentProfileId;
            return $this->updatePaymentProfile($parameters);
        }
    }

    /**
     * Get the customer profile
     *
     * @param array $parameters
     *
     * @return CIMGetProfileResponse
     */
    public function getProfile($parameters)
    {
        $obj = new CIMGetProfileRequest($this->httpClient, $this->httpRequest);
        $obj->initialize(array_replace($this->getParameters(), $parameters));
        return $obj->send();
    }

    /**
     * Makes an update profile request
     *
     * @param $parameters
     *
     * @return CIMCreateCardResponse
     */
    public function updatePaymentProfile($parameters)
    {
        $obj = new CIMUpdatePaymentProfileRequest($this->httpClient, $this->httpRequest);
        $obj->initialize(array_replace($this->getParameters(), $parameters));
        return $obj->send();
    }

}
