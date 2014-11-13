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

    public function testGetMatchingPaymentProfileId()
    {
        $httpResponse = $this->getMockHttpResponse('CIMGetProfileSuccess.txt');
        $response = new CIMGetProfileResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertEquals('26455656', $response->getMatchingPaymentProfileId('1111'));
        $this->assertEquals('26455709', $response->getMatchingPaymentProfileId('8888'));
        $this->assertNull($response->getMatchingPaymentProfileId('8889'));
    }
}
