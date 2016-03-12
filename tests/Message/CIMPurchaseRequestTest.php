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
        $this->assertEquals('12.00', $data->transactionRequest->amount);
        $this->assertEquals('28972085', $data->transactionRequest->profile->customerProfileId);
        $this->assertEquals('26317841', $data->transactionRequest->profile->paymentProfile->paymentProfileId);
        $this->assertEquals('27057151', $data->transactionRequest->profile->shippingProfileId);
        $this->assertEquals('Test purchase transaction', $data->transactionRequest->order->description);
    }
}
