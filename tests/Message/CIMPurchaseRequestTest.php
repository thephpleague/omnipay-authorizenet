<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMPurchaseRequestTest extends TestCase
{
    /** @var CIMPurchaseRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'cardReference' => '{"customerProfileId":"28972085","customerPaymentProfileId":"26317841","customerShippingAddressId":"27057151"}',
                'amount' => '12.00',
                'description' => 'Test purchase transaction'
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertObjectHasAttribute('profileTransAuthCapture',$data->transaction);
    }
}
