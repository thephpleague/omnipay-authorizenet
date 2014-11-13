<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMGetProfileRequestTest extends TestCase
{
    /** @var CIMGetProfileRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMGetProfileRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerProfileId' => '28775801',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('28775801', $data->customerProfileId);
    }
}