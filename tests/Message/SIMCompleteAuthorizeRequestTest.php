<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class SIMCompleteAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SIMCompleteAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $this->assertSame(md5(''), $this->request->getHash('', ''));

        $this->request->setHashSecret('hashsec');
        $this->request->setApiLoginId('apilogin');

        $this->assertSame(md5('hashsec' . 'apilogin' . 'trnref ' . '10.00'), $this->request->getHash('trnref ', '10.00'));
    }

    public function testSend()
    {
        $posted_trans_id = '12345'; // transactionReference in POST.
        $posted_amount = '10.00'; // amount authothorised in POST.

        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => $posted_trans_id,
                'x_amount' => $posted_amount,
                'x_MD5_Hash' => md5('shhh' . 'user' . $posted_trans_id . $posted_amount),
                'omnipay_transaction_id' => '99',
            )
        );
        $this->request->setApiLoginId('user');
        $this->request->setHashSecret('shhh');
        $this->request->setAmount('10.00');
        $this->request->setReturnUrl('http://example.com/');

        // Issue #22 Transaction ID in request is picked up from custom field.
        $this->assertSame('99', $this->request->getTransactionId());

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame($posted_trans_id, $response->getTransactionReference());
        $this->assertSame(true, $response->isRedirect());
        // CHECKME: does it matter what letter case the method is?
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('http://example.com/', $response->getRedirectUrl());
        $this->assertNull($response->getMessage());
    }
}
