<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Tests\TestCase;

class CIMCreatePaymentProfileResponseTest extends TestCase
{
    /**
     * @expectedException Omnipay\Common\Exception\InvalidResponseException
     */
    public function testConstructEmpty()
    {
        new CIMCreatePaymentProfileResponse($this->getMockRequest(), '');
    }

    public function testCreateCardSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('CIMCreatePaymentProfileSuccess.txt');
        $mockRequest = \Mockery::mock('\Omnipay\Common\Message\RequestInterface');
        $mockRequest->shouldReceive('getCustomerProfileId')->times(1)->andReturn('28775801');
        $response = new CIMCreatePaymentProfileResponse($mockRequest, $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('I00001', $response->getReasonCode());
        $this->assertEquals("1", $response->getResultCode());
        $this->assertEquals("Successful.", $response->getMessage());
        $this->assertEquals("26455709", $response->getCustomerPaymentProfileId());
        $this->assertEquals("28775801", $response->getCustomerProfileId());
    }

    public function testCreateCardFailure()
    {
        $httpResponse = $this->getMockHttpResponse('CIMCreatePaymentProfileFailure.txt');
        $response = new CIMCreatePaymentProfileResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('E00039', $response->getReasonCode());
        $this->assertEquals("3", $response->getResultCode());
        $this->assertEquals("A duplicate customer payment profile already exists.", $response->getMessage());
        $this->assertNull($response->getCardReference());
    }
}
