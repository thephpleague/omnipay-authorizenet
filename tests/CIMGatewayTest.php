<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Tests\GatewayTestCase;

class CIMGatewayTest extends GatewayTestCase
{
    /** @var CIMGateway */
    protected $gateway;
    protected $createCardOptions;
    protected $purchaseOptions;
    protected $captureOptions;
    protected $authorizeOptions;
    protected $refundOptions;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new CIMGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->initialize([
            'hashSecret' => 'HASHYsecretyThang',
        ]);

        $this->createCardOptions = array(
            'email' => "kaylee@serenity.com",
            'card' => $this->getValidCard(),
            'testMode' => true,
            'forceCardUpdate' => true
        );

        $this->authorizeOptions = array(
            'cardReference' => '{"customerProfileId":"28972084","customerPaymentProfileId":"26317840","customerShippingAddressId":"27057149"}',
            'amount' => 10.00,
            'description' => 'authorize'
        );

        $this->captureOptions = array(
            'amount' => '10.00',
            'description' => 'capture',
            'transactionReference' => '{"approvalCode":"DMK100","transId":"2220001902","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}',
        );

        $this->refundOptions = array(
            'amount' => '10.00',
            'transactionReference' => '{"approvalCode":"DMK100","transId":"2220001902","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}',
            'description' => 'refund'
        );
    }

    public function testLiveEndpoint()
    {
        $this->assertEquals(
            'https://api2.authorize.net/xml/v1/request.api',
            $this->gateway->getLiveEndpoint()
        );
    }

    public function testDeveloperEndpoint()
    {
        $this->assertEquals(
            'https://apitest.authorize.net/xml/v1/request.api',
            $this->gateway->getDeveloperEndpoint()
        );
    }

    // Added for PR #78
    public function testHashSecret()
    {
        $this->assertEquals(
            'HASHYsecretyThang',
            $this->gateway->getHashSecret()
        );
    }

    public function testCreateCardSuccess()
    {
        $this->setMockHttpResponse(array('CIMCreateCardSuccess.txt','CIMGetPaymentProfileSuccess.txt'));

        $response = $this->gateway->createCard($this->createCardOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"customerProfileId":"28972084","customerPaymentProfileId":"26485433"}',
            $response->getCardReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testShouldCreateCardIfDuplicateCustomerProfileExists()
    {
        $this->setMockHttpResponse(array('CIMCreateCardFailureWithDuplicate.txt', 'CIMCreatePaymentProfileSuccess.txt',
        'CIMGetProfileSuccess.txt', 'CIMGetPaymentProfileSuccess.txt'));

        $response = $this->gateway->createCard($this->createCardOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"customerProfileId":"28775801","customerPaymentProfileId":"26485433"}',
            $response->getCardReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testShouldUpdateExistingPaymentProfileIfDuplicateExistsAndForceCardUpdateIsSet()
    {
        // Duplicate **payment** profile
        $this->setMockHttpResponse(array('CIMCreateCardFailureWithDuplicate.txt', 'CIMCreatePaymentProfileFailure.txt',
            'CIMGetProfileSuccess.txt', 'CIMUpdatePaymentProfileSuccess.txt', 'CIMGetPaymentProfileSuccess.txt'));

        $response = $this->gateway->createCard($this->createCardOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"customerProfileId":"28775801","customerPaymentProfileId":"26485433"}',
            $response->getCardReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testShouldUpdateExistingPaymentProfileIfDuplicateExistsAndMaxPaymentProfileLimitIsMet()
    {
        $this->setMockHttpResponse(array('CIMCreateCardFailureWithDuplicate.txt',
            'CIMCreatePaymentProfileFailureMaxProfileLimit.txt', 'CIMGetProfileSuccess.txt',
            'CIMUpdatePaymentProfileSuccess.txt', 'CIMGetPaymentProfileSuccess.txt'));

        $response = $this->gateway->createCard($this->createCardOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"customerProfileId":"28775801","customerPaymentProfileId":"26485433"}',
            $response->getCardReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testCreateCardFailure()
    {
        $this->setMockHttpResponse('CIMCreateCardFailure.txt');

        $response = $this->gateway->createCard($this->createCardOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getCardReference());
        $this->assertSame('One or more fields in the profile must contain a value.', $response->getMessage());
    }

    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('AIMAuthorizeSuccess.txt');

        $response = $this->gateway->authorize($this->authorizeOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('{"approvalCode":"GA4OQP","transId":"2184493132","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('AIMAuthorizeFailure.txt');
        $response = $this->gateway->authorize($this->authorizeOptions)->send();
        $this->assertFalse($response->isSuccessful());

        $this->assertSame('{"approvalCode":"","transId":"0","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}', $response->getTransactionReference());
        $this->assertEquals("A valid amount is required.", $response->getMessage());
    }

    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('AIMCaptureSuccess.txt');

        $response = $this->gateway->capture($this->captureOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"F51OYG","transId":"2184494531"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('AIMCaptureFailure.txt');

        $response = $this->gateway->capture($this->captureOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0"}', $response->getTransactionReference());
        $this->assertSame('The transaction cannot be found.', $response->getMessage());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('AIMPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->authorizeOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"JE6JM1","transId":"2184492509","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('AIMPurchaseFailure.txt');

        $response = $this->gateway->purchase($this->authorizeOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
    }

    public function testRefundSuccess()
    {
        $this->markTestSkipped();
//        $this->setMockHttpResponse('CIMRefundSuccess.txt');
//
//        $response = $this->gateway->refund($this->refundOptions)->send();
//
//        $this->assertTrue($response->isSuccessful());
//        $this->assertSame('', $response->getTransactionReference());
//        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('AIMRefundFailure.txt');

        $response = $this->gateway->refund($this->refundOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0"}', $response->getTransactionReference());
        $this->assertSame(
            'The referenced transaction does not meet the criteria for issuing a credit.',
            $response->getMessage()
        );
    }

    public function testShouldVoidTransactionIfTryingToRefundAnUnsettledTransaction()
    {
        $this->setMockHttpResponse(array('AIMRefundFailure.txt', 'AIMVoidSuccess.txt'));
        $this->refundOptions['voidIfRefundFails'] = true;

        $response = $this->gateway->refund($this->refundOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals('This transaction has been approved.', $response->getMessage());
    }
}
