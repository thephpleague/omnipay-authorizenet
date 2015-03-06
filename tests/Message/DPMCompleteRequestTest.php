<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class DPMCompleteAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DPMCompleteRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrect hash
     */
    public function testGetDataInvalid()
    {
        $this->getHttpRequest()->request->replace(array('x_MD5_Hash' => 'invalid'));
        $this->request->getData();
    }

    public function testGetHash()
    {
        $this->assertSame(md5(''), $this->request->getHash());

        $this->request->setHashSecret('hashsec');
        $this->request->setApiLoginId('apilogin');
        $this->request->setTransactionId('trnid');
        $this->request->setAmount('10.00');

        $this->assertSame(md5('hashsecapilogintrnid10.00'), $this->request->getHash());
    }

    public function testSend()
    {
        // Note: the hash contains no data supplied by the merchant site, apart
        // from the secret. This is the first point at which we see the transaction
        // reference (x_trans_id), and this hash is to validate that the reference and
        // the amount have not be tampered with en-route.

        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '10.00',
                'x_MD5_Hash' => strtolower(md5('shhh' . 'user' . '12345' . '10.00')),
            )
        );
        $this->request->setApiLoginId('user');
        $this->request->setHashSecret('shhh');
        //$this->request->setAmount('10.00');
        //$this->request->setTransactionReference('12345');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }
}