<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMUpdatePaymentProfileResponseTest extends TestCase
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
        $httpResponse = $this->getMockHttpResponse('CIMUpdatePaymentProfileSuccess.txt');
        $mockRequest = \Mockery::mock('\Omnipay\Common\Message\RequestInterface');
        $mockRequest->shouldReceive('getCustomerProfileId')->times(1)->andReturn('28775801');
        $mockRequest->shouldReceive('getCustomerPaymentProfileId')->times(1)->andReturn('26455709');
        $response = new CIMUpdatePaymentProfileResponse($mockRequest, $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('I00001', $response->getReasonCode());
        $this->assertEquals("1", $response->getResultCode());
        $this->assertEquals("Successful.", $response->getMessage());
        $this->assertEquals(
            '{"customerProfileId":"28775801","customerPaymentProfileId":"26455709"}',
            $response->getCardReference()
        );
    }
}
