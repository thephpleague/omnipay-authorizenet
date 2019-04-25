<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMGetProfileResponseTest extends TestCase
{
    /**
     * @expectedException Omnipay\Common\Exception\InvalidResponseException
     */
    public function testConstructEmpty()
    {
        new CIMGetProfileResponse($this->getMockRequest(), '');
    }

    public function testGetMultipleMatchingPaymentProfileId()
    {
        $httpResponse = $this->getMockHttpResponse('CIMGetMultipleProfilesSuccess.txt');
        $response = new CIMGetProfileResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertEquals('26455656', $response->getMatchingPaymentProfileId('1111'));
        $this->assertEquals('26455709', $response->getMatchingPaymentProfileId('8888'));
        $this->assertNull($response->getMatchingPaymentProfileId('8889'));
    }

    public function testGetSingleMatchingPaymentProfileId()
    {
        $httpResponse = $this->getMockHttpResponse('CIMGetSingleProfileSuccess.txt');
        $response = new CIMGetProfileResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertEquals('26455656', $response->getMatchingPaymentProfileId('1111'));
        $this->assertNull($response->getMatchingPaymentProfileId('8889'));
    }
}
