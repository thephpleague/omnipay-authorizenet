<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class DPMPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        // The card for DPM will always start out blank, so remove the card details.

        $validCard = array_merge(
            $this->getValidCard(),
            array(
                'number' => '',
                'expiryMonth' => '',
                'expiryYear' => '',
                'cvv' => '',
            )
        );

        $this->request = new DPMPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'customerId' => 'cust-id',
                'card' => $validCard,
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

        $this->assertSame('', $data['x_card_num']);
        $this->assertSame('', $data['x_exp_date']);
        $this->assertSame('', $data['x_card_code']);

        $this->assertArrayNotHasKey('x_test_request', $data);
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);

        $data = $this->request->getData();

        $this->assertSame('TRUE', $data['x_test_request']);
    }

    public function testGetHash()
    {
        $this->request->setApiLoginId('user');
        $this->request->setTransactionKey('key');
        $data = array(
            'x_fp_sequence' => 'a',
            'x_fp_timestamp' => 'b',
            'x_amount' => 'c',
        );

        $expected = hash_hmac('md5', 'user^a^b^c^', 'key');

        $this->assertSame($expected, $this->request->createHash($data));
    }

    public function testSend()
    {
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());

        $redirectData = $response->getRedirectData();
        $this->assertSame('https://www.example.com/return', $redirectData['x_relay_url']);
    }
}
