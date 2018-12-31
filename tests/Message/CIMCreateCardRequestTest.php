<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMCreateCardRequestTest extends TestCase
{
    /** @var CIMCreateCardRequest */
    protected $request;
    private $params;

    public function setUp()
    {
        $this->request = new CIMCreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->params = array(
            'email' => "kaylee@serenity.com",
            'card' => $this->getValidCard(),
            'developerMode' => true
        );
        $this->request->initialize($this->params);
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $card = $this->params['card'];
        $this->assertEquals('12345', $data->profile->paymentProfiles->billTo->zip);
        $this->assertEquals($card['number'], $data->profile->paymentProfiles->payment->creditCard->cardNumber);
        $this->assertEquals('testMode', $data->validationMode);
    }

    public function testGetDataShouldHaveCustomBillTo()
    {
        unset($this->params['card']['billingAddress1']);
        unset($this->params['card']['billingAddress2']);
        unset($this->params['card']['billingCity']);
        $this->params['forceCardUpdate'] = true;
        $this->params['defaultBillTo'] = array(
            'address' => '1234 Test Street',
            'city' => 'Blacksburg'
        );
        $this->request->initialize($this->params);

        $data = $this->request->getData();

        $this->assertEquals('12345', $data->profile->paymentProfiles->billTo->zip);
        $this->assertEquals('1234 Test Street', $data->profile->paymentProfiles->billTo->address);
        $this->assertEquals('Blacksburg', $data->profile->paymentProfiles->billTo->city);
    }

    public function testGetDataShouldSetValidationModeToNoneIfNoCvvProvided()
    {
        unset($this->params['card']['cvv']);
        $this->request->initialize($this->params);

        $data = $this->request->getData();

        $this->assertFalse(isset($data->profile->paymentProfiles->payment->creditCard->cardCode));
        $this->assertEquals(CIMCreatePaymentProfileRequest::VALIDATION_MODE_NONE, $this->request->getValidationMode());
    }

    public function testGetDataOpaqueData()
    {

        $validCard = $this->getValidCard();
        unset($validCard['number'],$validCard['expiryMonth'],$validCard['expiryYear'],$validCard['cvv']);
        //remove the actual card data since we are setting opaque values
        $this->params = array(
            'email' => "kaylee@serenity.com",
            'card' => $validCard,
            'opaqueDataDescriptor' => 'COMMON.ACCEPT.INAPP.PAYMENT',
            'opaqueDataValue' => 'jb2RlIjoiNTB',
            'developerMode' => true
        );
        $this->request->initialize($this->params);

        $data = $this->request->getData();

        $this->assertEquals('COMMON.ACCEPT.INAPP.PAYMENT', $data->profile->paymentProfiles->payment->opaqueData->dataDescriptor);
        $this->assertEquals('jb2RlIjoiNTB', $data->profile->paymentProfiles->payment->opaqueData->dataValue);
    }
}
