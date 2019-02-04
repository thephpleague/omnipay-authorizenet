<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class AIMAuthorizeRequestTest extends TestCase
{
    /** @var AIMAuthorizeRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new AIMAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $card = $this->getValidCard();
        $card['email'] = 'example@example.net';
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'customerId' => 'cust-id',
                'card' => $card,
                'duplicateWindow' => 0,
                'solutionId' => 'SOL12345ID',
                'marketType' => '2',
                'deviceType' => '1',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('authOnlyTransaction', $data->transactionRequest->transactionType);
        $this->assertEquals('10.0.0.1', $data->transactionRequest->customerIP);
        $this->assertEquals('cust-id', $data->transactionRequest->customer->id);
        $this->assertEquals('example@example.net', $data->transactionRequest->customer->email);
        $this->assertEquals('SOL12345ID', $data->transactionRequest->solution->id);
        $this->assertEquals('2', $data->transactionRequest->retail->marketType);
        $this->assertEquals('1', $data->transactionRequest->retail->deviceType);

        // Issue #38 Make sure the transactionRequest properties are correctly ordered.
        // This feels messy, but works.
        $transactionRequestProperties = array_keys(get_object_vars($data->transactionRequest));
        // The names of the properies of the $data->transactionRequest object, in the order in
        // which they must be defined for Authorize.Net to accept the transaction.
        $keys = array(
            "transactionType",
            "amount",
            "payment",
            "solution",
            "order",
            "customer",
            "billTo",
            "shipTo",
            "customerIP",
            "retail",
            "transactionSettings"
        );
        $this->assertEquals($keys, $transactionRequestProperties);

        $setting = $data->transactionRequest->transactionSettings->setting[0];
        $this->assertEquals('testRequest', $setting->settingName);
        $this->assertEquals('false', $setting->settingValue);
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);

        $data = $this->request->getData();

        $setting = $data->transactionRequest->transactionSettings->setting[0];
        $this->assertEquals('testRequest', $setting->settingName);
        $this->assertEquals('true', $setting->settingValue);
    }

    public function testGetDataOpaqueData()
    {
        $this->request->setOpaqueDataDescriptor('COMMON.ACCEPT.INAPP.PAYMENT');
        $this->request->setOpaqueDataValue('jb2RlIjoiNTB');

        $data = $this->request->getData();

        $this->assertEquals('COMMON.ACCEPT.INAPP.PAYMENT', $data->transactionRequest->payment->opaqueData->dataDescriptor);
        $this->assertEquals('jb2RlIjoiNTB', $data->transactionRequest->payment->opaqueData->dataValue);
    }

    public function testGetDataTrack1()
    {
        $track1 = '%B5581123456781323^SMITH/JOHN^16071021473810559010203?';
        $track2 = ';5581123456781323=160710212423468?';

        $this->request->getCard()->setTracks($track1 . $track2);
        $data = $this->request->getData();

        $this->assertEquals(
            $track1,
            $data
                ->transactionRequest
                ->payment
                ->trackData
                ->track1
        );

        $this->assertEquals(
            $track2,
            $data
                ->transactionRequest
                ->payment
                ->trackData
                ->track2
        );

        // With track1 set, the card number must NOT be set.

        $this->assertNull($data
                ->transactionRequest
                ->payment
                ->creditCard
                ->cardNumber
        );
    }

    public function testShouldIncludeDuplicateWindowSetting()
    {
        $data = $this->request->getData();
        $setting = $data->transactionRequest->transactionSettings->setting[1];
        $this->assertEquals('duplicateWindow', $setting->settingName);
        $this->assertEquals('0', $setting->settingValue);
    }
}
