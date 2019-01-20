<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMCreatePaymentProfileRequestTest extends TestCase
{
    /** @var CIMCreatePaymentProfileRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMCreatePaymentProfileRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerProfileId' => '28775801',
                'email' => "kaylee@serenity.com",
                'card' => $this->getValidCard(),
                'developerMode' => true
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $card = $this->getValidCard();
        $this->assertEquals('28775801', $data->customerProfileId);
        $this->assertEquals($card['number'], $data->paymentProfile->payment->creditCard->cardNumber);
        $this->assertEquals('testMode', $data->validationMode);
    }

    public function testGetDataOpaqueData()
    {
        $validCard = $this->getValidCard();
        unset($validCard['number'],$validCard['expiryMonth'],$validCard['expiryYear'],$validCard['cvv']);
        //remove the actual card data since we are setting opaque values
        $this->request->initialize(
            array(
                'customerProfileId' => '28775801',
                'email' => "kaylee@serenity.com",
                'card' => $validCard,
                'opaqueDataDescriptor' => 'COMMON.ACCEPT.INAPP.PAYMENT',
                'opaqueDataValue' => 'jb2RlIjoiNTB',
                'developerMode' => true
            )
        );

        $data = $this->request->getData();
        $this->assertEquals('COMMON.ACCEPT.INAPP.PAYMENT', $data->paymentProfile->payment->opaqueData->dataDescriptor);
        $this->assertEquals('jb2RlIjoiNTB', $data->paymentProfile->payment->opaqueData->dataValue);
    }
}
