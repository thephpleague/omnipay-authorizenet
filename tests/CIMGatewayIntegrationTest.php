<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Tests\TestCase;
use Guzzle\Http\Client;
use Guzzle\Log\MessageFormatter;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;

/**
 * Integration tests for the CIM Gateway. These tests make real requests to Authorize.NET sandbox environment.
 *
 * In order to run, these tests require your Authorize.NET sandbox credentials without which, they just skip. Configure
 * the following environment variables:
 *
 *   1. AUTHORIZE_NET_API_LOGIN_ID
 *   2. AUTHORIZE_NET_TRANSACTION_KEY
 *
 * Once configured, the tests will no longer skip.
 */
class CIMGatewayIntegrationTest extends TestCase
{
    /** @var CIMGateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $apiLoginId = getenv('AUTHORIZE_NET_API_LOGIN_ID');
        $transactionKey = getenv('AUTHORIZE_NET_TRANSACTION_KEY');

        if ($apiLoginId && $transactionKey) {
            $this->gateway = new CIMGateway($this->getHttpClient(), $this->getHttpRequest());
            $this->gateway->setDeveloperMode(true);
            $this->gateway->setApiLoginId($apiLoginId);
            $this->gateway->setTransactionKey($transactionKey);
        } else {
            // No credentials were found, so skip this test
            $this->markTestSkipped();
        }
    }

    public function testIntegration()
    {
        // Create card
        $rand = rand(100000, 999999);
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "kaylee@serenity.com",
        );
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Profile should get created');
        $this->assertNotNull($response->getCardReference(), 'Card reference should be returned');

        $cardRef = $response->getCardReference();

        // Create Authorize only transaction
        $params = array(
            'cardReference' => $cardRef,
            'amount' => 100.00,
            'description'
        );
        $request = $this->gateway->authorize($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Authorize transaction should get created');
        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');

        $transRef = $response->getTransactionReference();

        // Capture the authorised transaction
        $params = array(
            'transactionReference' => $transRef,
            'amount' => 100.00,
        );
        $request = $this->gateway->capture($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Capture transaction should get created');
        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');
        $captureTransRef = $response->getTransactionReference();

        // Make a purchase using the saved card. i.e auth and capture
        $params = array(
            'cardReference' => $cardRef,
            'amount' => 110.00,
        );
        $request = $this->gateway->purchase($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Purchase transaction should get created');
        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');
        $purchaseTransRef = $response->getTransactionReference();

        // Make a refund on the purchase transaction
        $params = array(
            'transactionReference' => $purchaseTransRef,
            'amount' => 110.00,
        );
        $request = $this->gateway->refund($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        // todo: Fix refunds, and add unit tests using mocks
//        $this->assertTrue($response->isSuccessful(), 'Refund transaction should get created');
//        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');

    }
}
