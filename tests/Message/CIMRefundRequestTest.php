<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMRefundRequestTest extends TestCase
{
    /** @var CIMRefundRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMRefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionReference' => '{"approvalCode":"V7DO8Q","transId":"2220001612","cardReference":"{\"customerProfileId\":\"28972085\",\"customerPaymentProfileId\":\"26317841\",\"customerShippingAddressId\":\"27057151\"}"}',
                'amount' => 12.00,
                'description' => 'Test refund transaction'
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('12.00', $data->transaction->profileTransRefund->amount);
        $this->assertEquals('28972085', $data->transaction->profileTransRefund->customerProfileId);
        $this->assertEquals('26317841', $data->transaction->profileTransRefund->customerPaymentProfileId);
        $this->assertEquals('27057151', $data->transaction->profileTransRefund->customerShippingAddressId);
        $this->assertEquals(
            'Test refund transaction',
            $data->transaction->profileTransRefund->order->description
        );
        $this->assertEquals('2220001612', $data->transaction->profileTransRefund->transId);
    }
}