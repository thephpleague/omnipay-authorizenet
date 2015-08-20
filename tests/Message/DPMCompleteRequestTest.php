<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * The CompleteRequest object is invoked in the callback handler.
 */

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
        $this->assertSame(md5(''), $this->request->getHash('', ''));

        $this->request->setHashSecret('hashsec');
        $this->request->setApiLoginId('apilogin');

        $this->assertSame(md5('hashsec' . 'apilogin' . 'trnid' . '10.00'), $this->request->getHash('trnid', '10.00'));
    }

    public function testSend()
    {
        // The hash contains no data supplied by the merchant site, apart
        // from the secret. This is the first point at which we see the transaction
        // reference (x_trans_id), and this hash is to validate that the reference and
        // the amount have not be tampered with en-route.

        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '10.00',
                'x_MD5_Hash' => strtolower(md5('shhh' . 'user' . '12345' . '10.00')),
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
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertSame(true, $response->isRedirect());
        // CHECKME: does it matter what letter case the method is?
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('http://example.com/', $response->getRedirectUrl());
        $this->assertNull($response->getMessage());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrect amount
     */
    public function testSendWrongAmount()
    {
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

        // In the notify, the merchant application sets the amount that
        // was expected to be authorised. We expected 20.00 but are being
        // told it was 10.00.

        $this->request->setAmount('20.00');

        $response = $this->request->send();
    }
}