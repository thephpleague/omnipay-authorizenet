<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Tests\TestCase;

/**
 * Integration tests for the AIM Gateway. These tests make real requests to Authorize.NET sandbox environment.
 *
 * In order to run, these tests require your Authorize.NET sandbox credentials without which, they just skip. Configure
 * the following environment variables:
 *
 *   1. AUTHORIZE_NET_API_LOGIN_ID
 *   2. AUTHORIZE_NET_TRANSACTION_KEY
 *
 * Once configured, the tests will no longer skip.
 */
class AIMGatewayIntegrationTest extends TestCase
{
    /** @var AIMGateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $apiLoginId = getenv('AUTHORIZE_NET_API_LOGIN_ID');
        $transactionKey = getenv('AUTHORIZE_NET_TRANSACTION_KEY');

        if ($apiLoginId && $transactionKey) {
            $this->gateway = new AIMGateway($this->getHttpClient(), $this->getHttpRequest());
            $this->gateway->setDeveloperMode(true);
            $this->gateway->setApiLoginId($apiLoginId);
            $this->gateway->setTransactionKey($transactionKey);
        } else {
            // No credentials were found, so skip this test
            $this->markTestSkipped();
        }
    }

    public function testAuthCaptureVoid()
    {
        // Authorize
        $request = $this->gateway->authorize(array(
            'amount' => '42.42',
            'card' => $this->getValidCard()
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Authorization should succeed');
        $transactionRef = $response->getTransactionReference();

        // Capture
        $request = $this->gateway->capture(array(
            'amount' => '42.42',
            'transactionReference' => $transactionRef
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Capture should succeed');

        // Void
        $request = $this->gateway->void(array(
            'transactionReference' => $transactionRef
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Void should succeed');
    }

    public function testPurchaseRefundAutoVoid()
    {
        // Purchase
        $request = $this->gateway->purchase(array(
            'amount' => 10.01,
            'card' => $this->getValidCard()
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Purchase should succeed');
        $transactionRef = $response->getTransactionReference();

        // Refund (should fail)
        $request = $this->gateway->refund(array(
            'transactionReference' => $transactionRef,
            'amount' => 10.01
        ));
        $response = $request->send();
        $this->assertFalse($response->isSuccessful(), 'Refund should fail since the transaction has not been settled');

        // Refund with auto-void
        $request = $this->gateway->refund(array(
            'transactionReference' => $transactionRef,
            'amount' => 10.01,
            'voidIfRefundFails' => true
        ));
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Automatic void should succeed');
    }
}
