<?php


namespace Message;


use Omnipay\AuthorizeNet\Message\AIMAbstractRequest;

class AIMAbstractRequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var AIMAbstractRequest */
    private $request;

    public function setUp()
    {
        $this->request = $this->getMockForAbstractClass(
            '\Omnipay\AuthorizeNet\Message\AIMAbstractRequest',
            array(
                $this->getMock('\Guzzle\Http\ClientInterface'),
                $this->getMock('\Symfony\Component\HttpFoundation\Request')
            )
        );
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
