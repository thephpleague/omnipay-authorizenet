<?php

namespace Omnipay\AuthorizeNet\Model;

class CardReferenceTest extends \PHPUnit_Framework_TestCase
{
    private $data;
    /** @var CardReference */
    private $cardReference;

    public function setUp()
    {
        $this->data = '{"customerProfileId":"203830614","customerPaymentProfileId":"197483796","customerShippingAddressId":"768245213"}';
        $this->cardReference = new CardReference($this->data);
    }

    public function testShouldParseData()
    {
        $this->assertEquals('203830614', $this->cardReference->getCustomerProfileId());
        $this->assertEquals('197483796', $this->cardReference->getPaymentProfileId());
        $this->assertEquals('768245213', $this->cardReference->getShippingProfileId());
    }

    public function testShouldSerializeModel()
    {
        $actual = (string)$this->cardReference;
        $this->assertEquals($this->data, $actual);
    }
}
