<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class AIMResponseTest extends TestCase
{
    private $mockRequest;

    public function getMockRequest($className = '\Omnipay\AuthorizeNet\Message\AIMAbstractRequest')
    {
        if (null === $this->mockRequest) {
            $this->mockRequest = \Mockery::mock($className);
        }
        return $this->mockRequest;
    }

    public function testConstructEmpty()
    {
        $this->expectException('\Omnipay\Common\Exception\InvalidResponseException');

        new AIMResponse($this->getMockRequest(), '');
    }

    public function testAuthorizeSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMAuthorizeSuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"GA4OQP","transId":"2184493132"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame(1, $response->getResultCode());
        $this->assertSame(1, $response->getReasonCode());
        $this->assertSame('GA4OQP', $response->getAuthorizationCode());
        $this->assertSame('Y', $response->getAVSCode());
        $this->assertSame('P', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }

    public function testAuthorizeFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMAuthorizeFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0"}', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
        $this->assertSame(3, $response->getResultCode());
        $this->assertSame(5, $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }

    public function testAuthorizeInvalid()
    {
        $httpResponse = $this->getMockHttpResponse('AIMAuthorizeInvalid.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('', $response->getTransactionReference());
        $this->assertSame('User authentication failed due to invalid authentication values.', $response->getMessage());
        $this->assertSame(3, $response->getResultCode());
        $this->assertSame('E00007', $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
    }

    public function testAuthorizeInvalidOTSToken()
    {
        $httpResponse = $this->getMockHttpResponse('AIMAuthorizeInvalidOTSToken.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('E00114', $response->getReasonCode());
        $this->assertSame('Invalid OTS Token.', $response->getMessage());
    }

    public function testCaptureSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMCaptureSuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"F51OYG","transId":"2184494531"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame(1, $response->getResultCode());
        $this->assertSame(1, $response->getReasonCode());
        $this->assertSame('F51OYG', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }

    public function testCaptureFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMCaptureFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0"}', $response->getTransactionReference());
        $this->assertSame('The transaction cannot be found.', $response->getMessage());
        $this->assertSame(3, $response->getResultCode());
        $this->assertSame(16, $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('', $response->getAccountType());
    }
    
    public function testCaptureOnlySuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMCaptureOnlySuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"ROHNFQ","transId":"40009379672"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame(1, $response->getResultCode());
        $this->assertSame(1, $response->getReasonCode());
        $this->assertSame('ROHNFQ', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('MasterCard', $response->getAccountType());
    }

    public function testCaptureOnlyFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMCaptureOnlyFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"ROHNFQ","transId":"0"}', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
        $this->assertSame(3, $response->getResultCode());
        $this->assertSame(5, $response->getReasonCode());
        $this->assertSame('ROHNFQ', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('MasterCard', $response->getAccountType());
    }

    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMPurchaseSuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"JE6JM1","transId":"2184492509"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame(1, $response->getResultCode());
        $this->assertSame(1, $response->getReasonCode());
        $this->assertSame('JE6JM1', $response->getAuthorizationCode());
        $this->assertSame('Y', $response->getAVSCode());
        $this->assertSame('P', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMPurchaseFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0"}', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
        $this->assertSame(3, $response->getResultCode());
        $this->assertSame(5, $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }

    public function testRefundSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMRefundSuccess.txt');

        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"2217770693"}', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame(1, $response->getResultCode());
        $this->assertSame(1, $response->getReasonCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }

    public function testRefundFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMRefundFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('{"approvalCode":"","transId":"0"}', $response->getTransactionReference());
        $this->assertSame('The referenced transaction does not meet the criteria for issuing a credit.', $response->getMessage());
        $this->assertSame(3, $response->getResultCode());
        $this->assertSame(54, $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
        $this->assertSame('', $response->getCVVCode());
        $this->assertSame('Visa', $response->getAccountType());
    }
}
