<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Create Credit Card Request.
 */
class CIMCreateCardRequest extends CIMAbstractRequest
{
    protected $requestType = 'createCustomerProfileRequest';

    public function getData()
    {
        $this->validate('card');

        /** @var CreditCard $card */
        $card = $this->getCard();
        $card->validate();

        $data = $this->getBaseData();
        $this->addProfileData($data);
        $this->addTransactionSettings($data);

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

            $defaultBillTo = $this->getParameter('defaultBillTo');
            if (is_array($defaultBillTo)) {
                // A configuration parameter to populate billTo has been specified
                foreach ($defaultBillTo as $field => $value) {
                    if (empty($req->{$field}) && !empty($value)) {
                        // This particular field is empty and default value in populateBillTo has been specified
                        // so use it
                        $req->{$field} = $value;
                    }
                }
            }

            $req = $data->addChild('payment');
            $req->creditCard->cardNumber = $card->getNumber();
            $req->creditCard->expirationDate = $card->getExpiryDate('Y-m');
            if ($card->getCvv()) {
                $req->creditCard->cardCode = $card->getCvv();
            } else {
                $this->setValidationMode(self::VALIDATION_MODE_NONE);
            }
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

    protected function addTransactionSettings(\SimpleXMLElement $data)
    {
        $data->validationMode = $this->getValidationMode();
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        $response = new CIMCreateCardResponse($this, $httpResponse->getBody()->getContents());

        if (!$response->isSuccessful() && $response->getReasonCode() == 'E00039') {
            // Duplicate profile. Try adding a new payment profile for the same profile and get the response
            $response = $this->createPaymentProfile($response);
        } elseif ($response->isSuccessful()) {
            $parameters = array(
                'customerProfileId' => $response->getCustomerProfileId(),
                'customerPaymentProfileId' => $response->getCustomerPaymentProfileId()
            );
            // Get the payment profile for the specified card.
            $response = $this->makeGetPaymentProfileRequest($parameters);
        }

        $response->augmentResponse();
        return $this->response = $response;
    }

    /**
     * Attempts to add a payment profile to the existing customer profile and return the updated customer profile
     *
     * @param CIMCreateCardResponse $createCardResponse Duplicate customer profile response
     *
     * @return CIMCreateCardResponse
     */
    public function createPaymentProfile(CIMCreateCardResponse $createCardResponse)
    {
        // Parse the customer profile Id from the message
        $msg = $createCardResponse->getMessage();
        preg_match("/ID (.+) already/i", $msg, $matches);
        if (empty($matches[1])) {
            // Duplicate profile id not found. Return current response
            return $createCardResponse;
        }

        // Use the customerProfileId and create a payment profile for the customer
        $parameters = array_replace($this->getParameters(), array('customerProfileId' => $matches[1]));
        $createPaymentProfileResponse = $this->makeCreatePaymentProfileRequest($parameters);
        if ($createPaymentProfileResponse->isSuccessful()) {
            $parameters['customerPaymentProfileId'] = $createPaymentProfileResponse->getCustomerPaymentProfileId();
        } elseif ($this->getForceCardUpdate() !== true) {
            // force card update flag turned off. No need to further process.
            return $createCardResponse;
        }

        $getProfileResponse = $this->makeGetProfileRequest($parameters);

        // Check if there is a pre-existing profile for the given card numbers.
        // For these codes we should check for duplicate payment profiles
        $otherErrorCodes = array(
            CIMGetProfileResponse::ERROR_DUPLICATE_PROFILE,
            CIMGetProfileResponse::ERROR_MAX_PAYMENT_PROFILE_LIMIT_REACHED
        );
        if (!$createPaymentProfileResponse->isSuccessful() &&
            in_array($createPaymentProfileResponse->getReasonCode(), $otherErrorCodes)
        ) {
            // There is a possibility of a duplicate payment profile, so find matching payment profile id
            // from the customer profile and update it.
            $card = $this->getCard();
            $last4 = substr($card->getNumber(), -4);

            $customerPaymentProfileId = $getProfileResponse->getMatchingPaymentProfileId($last4);

            if (!$customerPaymentProfileId) {
                // Failed. Matching customer payment profile id not found. Return the original response
                return $createCardResponse;
            }

            $parameters['customerPaymentProfileId'] = $customerPaymentProfileId;
            $updatePaymentProfileResponse = $this->makeUpdatePaymentProfileRequest($parameters);
            if (!$updatePaymentProfileResponse->isSuccessful()) {
                // Could not update payment profile. Return the original response
                return $createCardResponse;
            }
        }

        // return the updated customer profile
        $getPaymentProfileResponse = $this->makeGetPaymentProfileRequest($parameters);
        if (!$getPaymentProfileResponse->isSuccessful()) {
            // Could not get the updated customer profile. Return the original response
            return $createCardResponse;
        }

        return $getPaymentProfileResponse;
    }

    /**
     * Make a request to add a payment profile to the current customer profile
     *
     * @param $parameters
     *
     * @return CIMCreatePaymentProfileResponse
     */
    public function makeCreatePaymentProfileRequest($parameters)
    {
        $obj = new CIMCreatePaymentProfileRequest($this->httpClient, $this->httpRequest);
        $obj->initialize(array_replace($this->getParameters(), $parameters));
        return $obj->send();
    }

    /**
     * Get the customer profile
     *
     * @param array $parameters
     *
     * @return CIMGetProfileResponse
     */
    public function makeGetProfileRequest($parameters)
    {
        $obj = new CIMGetProfileRequest($this->httpClient, $this->httpRequest);
        $obj->initialize(array_replace($this->getParameters(), $parameters));
        return $obj->send();
    }

    /**
     * Get the customer payment profile
     *
     * @param array $parameters
     *
     * @return CIMGetPaymentProfileResponse
     */
    public function makeGetPaymentProfileRequest($parameters)
    {
        $obj = new CIMGetPaymentProfileRequest($this->httpClient, $this->httpRequest);
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
    public function makeUpdatePaymentProfileRequest($parameters)
    {
        $obj = new CIMUpdatePaymentProfileRequest($this->httpClient, $this->httpRequest);
        $obj->initialize(array_replace($this->getParameters(), $parameters));
        return $obj->send();
    }
}
