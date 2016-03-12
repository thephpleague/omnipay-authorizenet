<?php

namespace Omnipay\AuthorizeNet\Model;

class TransactionReferenceTest extends \PHPUnit_Framework_TestCase
{
    private $data;
    /** @var TransactionReference */
    private $transactionReference;

    public function setUp()
    {
        $this->data = '{"approvalCode":"GA4OQP","transId":"2184493132","cardReference":"{\"customerProfileId\":\"28972084\",\"customerPaymentProfileId\":\"26317840\",\"customerShippingAddressId\":\"27057149\"}"}';
        $this->transactionReference = new TransactionReference($this->data);
    }

    public function testShouldParseData()
    {
        $this->assertEquals('GA4OQP', $this->transactionReference->getApprovalCode());
        $this->assertEquals('2184493132', $this->transactionReference->getTransId());
        $cardReference = $this->transactionReference->getCardReference();
        $this->assertEquals('28972084', $cardReference->getCustomerProfileId());
        $this->assertEquals('26317840', $cardReference->getPaymentProfileId());
        $this->assertEquals('27057149', $cardReference->getShippingProfileId());
    }

    public function testShouldSerializeModel()
    {
        $this->assertEquals($this->data, (string)$this->transactionReference);
    }
}
