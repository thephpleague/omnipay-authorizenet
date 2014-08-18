<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

class AIMRefundRequestTest extends TestCase
{
    /** @var AIMRefundRequest */
    private $request;

    public function setUp()
    {
        $this->request = new AIMRefundRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetDataMissingCardInfo()
    {
        $this->request->initialize(
            array(
                'transactionReference' => '123',
                'amount' => '12.00'
            )
        );

        try {
            $this->request->getData();
        } catch (InvalidRequestException $irex) {
            $this->assertEquals($irex->getMessage(), "The card parameter is required");
            return;
        } catch (\Exception $e) {
            $this->fail("Invalid exception was thrown: " . $e->getMessage());
            return;
        }

        $this->fail("InvalidRequestException should get thrown because card is missing");
    }

    public function testGetData()
    {
        $this->request->initialize(
            array(
                'transactionReference' => 'authnet-transaction-reference',
                'amount' => 12.12,
                'card' => array(
                    'number' => 1111,   // Refunds require only the last 4 digits of the credit card
                    'expiryMonth' => 5,
                    'expiryYear' => 2020
                )
            )
        );

        $data = $this->request->getData();

        $this->assertEquals('refundTransaction', $data->transactionRequest->transactionType);
        $this->assertEquals('12.12', (string)$data->transactionRequest->amount[0]);
        $this->assertEquals('authnet-transaction-reference', $data->transactionRequest->refTransId);

        $setting = $data->transactionRequest->transactionSettings->setting[0];
        $this->assertEquals('testRequest', $setting->settingName);
        $this->assertEquals('false', $setting->settingValue);
    }
}
