<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMResponseTest extends TestCase
{

    /**
     * @expectedException Omnipay\Common\Exception\InvalidResponseException
     */
    public function testConstructEmpty()
    {
        new CIMResponse($this->getMockRequest(), '');
    }

    public function testGetTransactionReference()
    {
        $httpResponse = $this->getMockHttpResponse('CIMAuthorizeSuccess.txt');
        $mockRequest = \Mockery::mock('\Omnipay\Common\Message\RequestInterface');
        $mockRequest->shouldReceive('getCardReference')->times(1)->andReturn(
            '{"customerProfileId":"28972085","customerPaymentProfileId":"26317841","customerShippingAddressId":"27057151"}'
        );
        $response = new CIMResponse($mockRequest, $httpResponse->getBody());

        $this->assertEquals(
            '{"approvalCode":"DMK100","transId":"2220001902","cardReference":"{\"customerProfileId\":\"28972085\",\"customerPaymentProfileId\":\"26317841\",\"customerShippingAddressId\":\"27057151\"}"}',
            $response->getTransactionReference()
        );
    }
}
