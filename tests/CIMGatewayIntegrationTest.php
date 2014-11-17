<?php

namespace Omnipay\AuthorizeNet;

use Guzzle\Http\Client;
use Omnipay\AuthorizeNet\Message\CIMResponse;
use Omnipay\Tests\TestCase;

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
            $client = new Client();

            $this->gateway = new CIMGateway($client, $this->getHttpRequest());
            $this->gateway->setDeveloperMode(true);
            $this->gateway->setApiLoginId($apiLoginId);
            $this->gateway->setTransactionKey($transactionKey);
        } else {
            // No credentials were found, so skip this test
            $this->markTestSkipped();
        }
    }

    public function testCreateCustomerAndPaymentProfile()
    {
        // Create card
        $rand = uniqid();
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "kaylee$rand@serenity.com",
        );
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);

        /* @var $response CIMResponse */
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Profile should get created');
        $this->assertNotNull($response->getCardReference(), 'Card reference should be returned');

        $cardRef = $response->getCardReference();

        // Try creating a different card for the same user
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "kaylee$rand@serenity.com",
        );
        $params['card']['number'] = '4007000000027';
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Should be successful as we have created a payment profile');
        $this->assertNotNull($response->getCardReference(), 'Card reference should be returned');

        // Try creating same card for the same user without force card update flag.
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "kaylee$rand@serenity.com",
        );
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertFalse($response->isSuccessful(), 'Should fail as we tried creating a duplicate profile');
        $this->assertNull($response->getCardReference(), 'Card reference should be returned');

        // Try creating same card for the same user again with force card update flag.
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "kaylee$rand@serenity.com",
            'forceCardUpdate' => true
        );
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Should succeed in updating the existing payment profile');
        $this->assertEquals(
            $cardRef,
            $response->getCardReference(),
            'Card reference should be same as with the one newly created'
        );
    }

    public function testAuthorizeAndVoid()
    {
        // Create card
        $rand = uniqid();
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye ' . $rand,
            'email' => "kaylee$rand@serenity.com",
        );
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);

        /* @var $response CIMResponse */
        $response = $request->send();
        $this->assertNotNull($response->getCardReference(), 'Card reference should be returned');

        $cardRef = $response->getCardReference();

        // Create Authorize only transaction
        $params = array(
            'cardReference' => $cardRef,
            'amount' => 100.00,
            'description' => 'Hello World'
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

        $transactionRef = json_decode($response->getTransactionReference(), true);

        // Make a refund on the purchase transaction
        $params = array('transactionReference' => $transactionRef['transId']);
        $request = $this->gateway->void($params);
        $request->setDeveloperMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Refund transaction should get created');
        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');
    }
}
