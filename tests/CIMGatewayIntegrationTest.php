<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Common\Http\Client;
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

    /**
     * Helper method to create a card on CIM.
     *
     * @param array $params
     * @return string
     */
    private function createCard($params = array())
    {
        $rand = uniqid('', true);
        $defaults = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "$rand@serenity.com",
        );
        $params = array_merge($defaults, $params);
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);
        /* @var $response CIMResponse */
        $response = $request->send();
        return $response->getCardReference();
    }

    public function testCustomerAndPaymentProfile()
    {
        // Create a customer profile with the specified email (email is the identifier)
        $email = uniqid('', true) . '@example.com';
        $valid_card = $this->getValidCard();
        $cardRef = $this->createCard(
            array(
                'email' => $email,
                'card' => $valid_card
            )
        );
        $decodedCardRef = json_decode($cardRef,true);

        // Create a new card in an existing customer profile
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => $email,
        );
        $params['card']['number'] = '4007000000027';
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Should be successful as we have created a payment profile');
        $this->assertNotNull($response->getCardReference(), 'Card reference should be returned');

        // Create a new card in an existing customer profile using its customer profile ID
        $params = array(
            'card' => $this->getValidCard(),
            'customerProfileId' => $decodedCardRef['customerProfileId']
        );
        $params['card']['number'] = '4012888818888';
        $request = $this->gateway->createAdditionalCard($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Should be successful as we have created a payment profile');
        $this->assertNotNull($response->getCardReference(), 'Card reference should be returned');

        // Create a card with same number in an existing customer profile (should fail)
        $params = array(
            'card' => $valid_card,
            'name' => 'Kaywinnet Lee Frye',
            'email' => $email,
        );
        $request = $this->gateway->createCard($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertFalse($response->isSuccessful(), 'Should fail as we tried creating a duplicate profile');
        $this->assertNull($response->getCardReference(), 'Card reference should be returned');

        // Create a card with the same number in an existing customer profile with auto-update enabled
        $params = array(
            'card' => $valid_card,
            'name' => 'Kaywinnet Lee Frye',
            'email' => $email,
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

    public function testGetCustomerProfile()
    {
        // Create a customer profile with the specified email (email is the identifier)
        $email = uniqid('', true) . '@example.com';
        $cardRef = $this->createCard(array('email' => $email));
        $cardRef = json_decode($cardRef,true);
        // Grab the customer Profile ID from the createCard response.
        $params = array(
            'customerProfileId' => $cardRef['customerProfileId']
        );
        // Return just the customer profile without billing data
        $request = $this->gateway->getCustomerProfile($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Should be successful.');
        $data = $response->getData();
        $this->assertEquals(
            $email,
            $data['profile']['email'],
            'Should be the same email'
        );
    }

    public function testPaymentProfileDelete()
    {

        // Create a customer profile with the specified email (email is the identifier) (to have a deletable payment profile)
        $email = uniqid('', true) . '@example.com';
        $cardRef = $this->createCard(array('email' => $email));
        $cardRef = json_decode($cardRef,true);

        //Delete the recently created payment profile (deletes the payment profile only, not the customer profile)
        $params = array(
            'customerProfileId' => $cardRef['customerProfileId'],
            'customerPaymentProfileId' => $cardRef['customerPaymentProfileId']
        );
        $defaults = array(  );
        $params = array_merge($defaults, $params);
        $request = $this->gateway->deleteCard($params);
        $request->setDeveloperMode(true);
        /* @var $response CIMResponse */
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Should be successful as we have deleted a payment profile');

        /* retrieve the recently deleted payment profile for the customer profile from authorize.net (returns NULL) */
        $params = array(
            'customerProfileId' => $cardRef['customerProfileId'],
            'customerPaymentProfileId' => $cardRef['customerPaymentProfileId']
        );
        $defaults = array(  );
        $params = array_merge($defaults, $params);
        $request = $this->gateway->getPaymentProfile($params);
        $request->setDeveloperMode(true);
        /* @var $response CIMResponse */
        $response = $request->send();
        $this->assertNull($response->getCustomerPaymentProfileId(), 'Should be null as we have deleted that payment profile');
    }

    public function testAuthorizeCapture()
    {
        $cardRef = $this->createCard();

        // Authorize
        $params = array(
            'cardReference' => $cardRef,
            'amount' => 100.00,
            'description' => 'Hello World'
        );
        $request = $this->gateway->authorize($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Authorize transaction should get created');
        $transRef = $response->getTransactionReference();
        $this->assertNotNull($transRef, 'Transaction reference should exist');

        // Capture
        $params = array(
            'transactionReference' => $transRef,
            'amount' => 100.00,
        );
        $request = $this->gateway->capture($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Capture transaction should get created');
        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');
    }

    public function testPurchaseRefundAutoVoid()
    {
        $cardRef = $this->createCard();

        // Purchase
        $params = array(
            'cardReference' => $cardRef,
            'amount' => 110.00,
        );
        $request = $this->gateway->purchase($params);
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Purchase transaction should get created');
        $transactionReference = $response->getTransactionReference();
        $this->assertNotNull($transactionReference, 'Transaction reference should exist');

        // Refund (should fail)
        $request = $this->gateway->refund(array(
            'amount' => 110.00,
            'transactionReference' => $transactionReference));
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertFalse($response->isSuccessful(), 'Refund should fail since the transaction has not been settled');

        // Refund with auto-void
        $request = $this->gateway->refund(array(
            'amount' => 110.00,
            'transactionReference' => $transactionReference,
            'voidIfRefundFails' => true));
        $request->setDeveloperMode(true);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Refund should succeed as a void transaction');
        $this->assertNotNull($response->getTransactionReference(), 'Transaction reference should exist');
    }
}
