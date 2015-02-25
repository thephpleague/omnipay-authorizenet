<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class DPMPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DPMPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'customerId' => 'cust-id',
                'card' => $this->getValidCard(),
                'returnUrl' => 'https://www.example.com/return',
                'liveEndpoint'      => 'https://secure.authorize.net/gateway/transact.dll',
                'developerEndpoint' => 'https://test.authorize.net/gateway/transact.dll',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('AUTH_CAPTURE', $data['x_type']);
        $this->assertSame('10.0.0.1', $data['x_customer_ip']);
        $this->assertSame('cust-id', $data['x_cust_id']);
        $this->assertArrayNotHasKey('x_test_request', $data);
    }
}
