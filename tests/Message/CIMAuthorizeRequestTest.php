<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMAuthorizeRequestTest extends TestCase
{
    /** @var CIMAuthorizeRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'cardReference' => '{"customerProfileId":"28972085","customerPaymentProfileId":"26317841","customerShippingAddressId":"27057151"}',
                'amount' => '12.00',
                'description' => 'Test authorize transaction'
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('12.00', $data->transaction->profileTransAuthOnly->amount);
        $this->assertEquals('28972085', $data->transaction->profileTransAuthOnly->customerProfileId);
        $this->assertEquals('26317841', $data->transaction->profileTransAuthOnly->customerPaymentProfileId);
        $this->assertEquals('27057151', $data->transaction->profileTransAuthOnly->customerShippingAddressId);
        $this->assertEquals('Test authorize transaction', $data->transaction->profileTransAuthOnly->order->description);
    }
}
