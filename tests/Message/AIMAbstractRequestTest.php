<?php

namespace Message;

use Omnipay\AuthorizeNet\Message\AIMAbstractRequest;
use Omnipay\Tests\TestCase;
use Mockery;

class AIMAbstractRequestTest extends TestCase
{
    /** @var AIMAbstractRequest */
    private $request;

    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\AuthorizeNet\Message\AIMAbstractRequest')->makePartial();
        $this->request->initialize();
    }

    public function testShouldReturnTransactionReference()
    {
        $complexKey = json_encode(array('transId' => 'TRANS_ID', 'cardReference' => 'CARD_REF'));
        $this->request->setTransactionReference($complexKey);
        $this->assertEquals('TRANS_ID', $this->request->getTransactionReference()->getTransId());
    }

    public function testShouldReturnBackwardCompatibleTransactionReference()
    {
        $this->request->setTransactionReference('TRANS_ID');
        $this->assertEquals('TRANS_ID', $this->request->getTransactionReference()->getTransId());
    }
}
