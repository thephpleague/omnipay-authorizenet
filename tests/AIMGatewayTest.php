<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Tests\GatewayTestCase;

class AIMGatewayTest extends GatewayTestCase
{
    /** @var AIMGateway */
    protected $gateway;
    protected $purchaseOptions;
    protected $captureOptions;
    protected $voidOptions;
    protected $refundOptions;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new AIMGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->purchaseOptions = array(
            'amount' => '10.00',
            'card' => $this->getValidCard(),
        );

        $this->captureOptions = array(
            'amount' => '10.00',
            'transactionReference' => '12345',
        );

        $this->voidOptions = array(
            'transactionReference' => '12345',
        );

        $this->refundOptions = array(
            'amount' => '10.00',
            'transactionReference' => '12345',
            'card' => $this->getValidCard()
        );
    }

    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('AIMAuthorizeSuccess.txt');

        $response = $this->gateway->authorize($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2184493132', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('AIMAuthorizeFailure.txt');

        $response = $this->gateway->authorize($this->purchaseOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
    }

    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('AIMCaptureSuccess.txt');

        $response = $this->gateway->capture($this->captureOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2184494531', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('AIMCaptureFailure.txt');

        $response = $this->gateway->capture($this->captureOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('The transaction cannot be found.', $response->getMessage());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('AIMPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2184492509', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('AIMPurchaseFailure.txt');

        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
    }

    public function testVoidSuccess()
    {
        $this->setMockHttpResponse('AIMVoidSuccess.txt');

        $response = $this->gateway->void($this->voidOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('This transaction has already been voided.', $response->getMessage());
    }

    public function testVoidFailure()
    {
        $this->setMockHttpResponse('AIMVoidFailure.txt');

        $response = $this->gateway->void($this->voidOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('A valid referenced transaction ID is required.', $response->getMessage());
    }

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('AIMRefundSuccess.txt');

        $response = $this->gateway->refund($this->refundOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2217770693', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('AIMRefundFailure.txt');

        $response = $this->gateway->refund($this->refundOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('The referenced transaction does not meet the criteria for issuing a credit.', $response->getMessage());
    }
}
