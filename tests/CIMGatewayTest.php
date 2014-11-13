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

        $this->createCardOptions = array(
            'email' => "kaylee@serenity.com",
            'card' => $this->getValidCard(),
            'testMode' => true
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

        $this->authorizeOptions = array(
            'cardReference' => '{"customerProfileId":"28972084","customerPaymentProfileId":"26317840","customerShippingAddressId":"27057149"}',
            'amount' => 10.00,
            'description' => 'purchase'
        );

        $this->refundOptions = array(
            'amount' => '10.00',
            'transactionReference' => '{"approvalCode":"DMK100","transId":"2220001902","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}',
            'description' => 'refund'
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
        $this->setMockHttpResponse('CIMAuthorizeSuccess.txt');

        $response = $this->gateway->authorize($this->authorizeOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"approvalCode":"DMK100","transId":"2220001902","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}',
            $response->getTransactionReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('CIMAuthorizeFailure.txt');

        try {
            $response = $this->gateway->authorize($this->authorizeOptions)->send();
        } catch(\Exception $e) {

        }

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame(
            "The 'AnetApi/xml/v1/schema/AnetApiSchema.xsd:amount' element is invalid - The value '-100.00' is invalid according to its datatype 'Decimal' - The MinInclusive constraint failed.",
            $response->getMessage()
        );
    }

    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('CIMCaptureSuccess.txt');

        $response = $this->gateway->capture($this->captureOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"approvalCode":"DMK100","transId":"2220001903","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}',
            $response->getTransactionReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('CIMCaptureFailure.txt');

        $response = $this->gateway->capture($this->captureOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('Approval Code is required.', $response->getMessage());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('CIMPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->authorizeOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(
            '{"approvalCode":"MNLXJQ","transId":"2220001904","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}',
            $response->getTransactionReference()
        );
        $this->assertSame('Successful.', $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('CIMPurchaseFailure.txt');

        $response = $this->gateway->purchase($this->authorizeOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull(
            $response->getTransactionReference()
        );
        $this->assertSame('This transaction has been declined.', $response->getMessage());
    }

    public function testRefundSuccess()
    {
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
        $this->setMockHttpResponse('CIMRefundFailure.txt');

        $response = $this->gateway->refund($this->refundOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull(
            $response->getTransactionReference()
        );
        $this->assertSame(
            'The referenced transaction does not meet the criteria for issuing a credit.',
            $response->getMessage()
        );
    }
}
