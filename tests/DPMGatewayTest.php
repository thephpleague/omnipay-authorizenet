<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\GatewayTestCase;

class DPMGatewayTest extends GatewayTestCase
{
    /** @var DPMGateway */
    protected $gateway;
    /** @var array */
    private $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new DPMGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setApiLoginId('example');
        $this->gateway->setTransactionKey('keykey');
        $this->gateway->setHashSecret('elpmaxe');

        $this->options = array(
            'amount' => '10.00',
            'transactionId' => '99',
            'returnUrl' => 'https://www.example.com/return',
        );
    }

    public function testLiveEndpoint()
    {
        $this->assertEquals(
            'https://secure2.authorize.net/gateway/transact.dll',
            $this->gateway->getLiveEndpoint()
        );
    }

    public function testDeveloperEndpoint()
    {
        $this->assertEquals(
            'https://test.authorize.net/gateway/transact.dll',
            $this->gateway->getDeveloperEndpoint()
        );
    }

    public function testAuthorize()
    {
        $response = $this->gateway->authorize($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());

        $redirectData = $response->getRedirectData();
        $this->assertSame('https://www.example.com/return', $redirectData['x_relay_url']);
    }

    /**
     * The MD4 Hash consists of the shared secret, the login ID, the transaction *reference* (as
     * generated on the remote gateway for the transaction) and the amount.
     */
    public function testCompleteAuthorize()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '10.00',
                'x_MD5_Hash' => md5('elpmaxe' . 'example' . '12345' . '10.00'),
            )
        );

        $response = $this->gateway->completeAuthorize($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrect amount
     *
     * The hash is correct, so the sender knows the shared secret, but the amount
     * is not what we expected, i.e. what we had requested to be authorized.
     */
    public function testCompleteAuthorizeWrongAmount()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '20.00',
                'x_MD5_Hash' => md5('elpmaxe' . 'example' . '12345' . '20.00'),
            )
        );

        $response = $this->gateway->completeAuthorize($this->options)->send();
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());

        $redirectData = $response->getRedirectData();
        $this->assertSame('https://www.example.com/return', $redirectData['x_relay_url']);
    }

    public function testCompletePurchase()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '10.00',
                'x_MD5_Hash' => md5('elpmaxe' . 'example' . '12345' . '10.00'),
            )
        );

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }
}
