<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class SIMCompleteRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SIMCompleteRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
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

    public function testGetMd5Hash()
    {
        $this->assertSame(md5(''), $this->request->getHash());

        $this->request->setHashSecret('hashsec');
        $this->request->setApiLoginId('apilogin');

        $this->getHttpRequest()->request->replace(
            array(
                'x_trans_id' => 'trnref',
                'x_amount' => '10.00',
            )
        );

        $this->assertSame(
            md5('hashsec' . 'apilogin' . 'trnref' . '10.00'),
            $this->request->getHash()
        );
    }

    public function testGetSha512Hash()
    {
        $this->request->setSignatureKey('48D2C629E4AE7CA3C4E6CD7223DA');

        $this->getHttpRequest()->request->replace(
            array(
                'x_trans_id' => 'trn123456',
                'x_test_request' => 'xxx',
                'x_response_code' => 'xxx',
                'x_auth_code' => 'xxx',
                'x_cvv2_resp_code' => 'xxx',
                'x_cavv_response' => 'xxx',
                'x_avs_code' => 'xxx',
                'x_method' => 'xxx',
                'x_account_number' => 'xxx',
                'x_amount' => '10.99',
                'x_company' => 'xxx',
                'x_first_name' => 'xxx',
                'x_last_name' => 'xxx',
                'x_address' => 'xxx',
                'x_city' => 'xxx',
                'x_state' => 'xxx',
                'x_zip' => 'xxx',
                'x_country' => 'xxx',
                'x_phone' => 'xxx',
                'x_fax' => 'xxx',
                'x_email' => 'xxx',
                'x_ship_to_company' => 'xxx',
                'x_ship_to_first_name' => 'xxx',
                'x_ship_to_last_name' => 'xxx',
                'x_ship_to_address' => 'xxx',
                'x_ship_to_city' => 'xxx',
                'x_ship_to_state' => 'xxx',
                'x_ship_to_zip' => 'xxx',
                'x_ship_to_country' => 'xxx',
                'x_invoice_num' => 'xxx',
            )
        );

        $this->assertSame(
            'F9A0DE7A9AC83E0B8043CD7CBD804ED41FE6BFDDB2C10C486DB4E3C4F3E7163237837A5CD6AEE1FAFF03BAD076DF287F7E81E17ED38752999D1AA6249ECC1613',
            $this->request->getHash()
        );
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
