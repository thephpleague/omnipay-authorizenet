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
                'testMode' => true
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('Example', $data->profile->paymentProfiles->billTo->firstName);
        $this->assertEquals('4111111111111111', $data->profile->paymentProfiles->payment->creditCard->cardNumber);
        $this->assertEquals('testMode', $data->validationMode);
    }
}
