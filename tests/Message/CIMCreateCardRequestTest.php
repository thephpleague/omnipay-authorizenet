<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMCreateCardRequestTest extends TestCase
{
    /** @var CIMCreateCardRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMCreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
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
        $this->assertEquals('12345', $data->profile->paymentProfiles->billTo->zip);
        $this->assertEquals($card['number'], $data->profile->paymentProfiles->payment->creditCard->cardNumber);
        $this->assertEquals('testMode', $data->validationMode);
    }
}
