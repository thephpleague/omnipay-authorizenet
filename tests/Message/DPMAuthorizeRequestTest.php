<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class DPMAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DPMAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'returnUrl' => 'https://www.example.com/return',
                'liveEndpoint'      => 'https://secure.authorize.net/gateway/transact.dll',
                'developerEndpoint' => 'https://test.authorize.net/gateway/transact.dll',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('AUTH_ONLY', $data['x_type']);
        $this->assertArrayNotHasKey('x_show_form', $data);
        $this->assertArrayNotHasKey('x_test_request', $data);
    }

    /**
     * Issue #123 test the signature key.
     */
    public function testSha512hash()
    {
        $signatureKey = 
            '48D2C629E4A6D3E19AC47767C8B7EFEA4AE2004F8FA9C190F19D0238D871978B'
            . 'E35925A6AD9256FE623934C1099DFEFD6449D54744E5734CE7CA3C4E6CD7223D';

        $this->request->setSignatureKey($signatureKey);

        $data = $this->request->getData();
        $hash = $data['x_fp_hash'];

        // Now check the hash.

        $fingerprint = implode(
            '^',
            array(
                $this->request->getApiLoginId(),
                $data['x_fp_sequence'],
                $data['x_fp_timestamp'],
                $data['x_amount']
            )
        ).'^';

        $this->assertTrue(
            hash_equals(hash_hmac('sha512', $fingerprint, hex2bin($signatureKey)), $hash)
        );
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);

        $data = $this->request->getData();

        $this->assertSame('TRUE', $data['x_test_request']);
    }

    public function testGetHash()
    {
        $this->request->setApiLoginId('user');
        $this->request->setTransactionKey('key');
        $data = array(
            'x_fp_sequence' => 'a',
            'x_fp_timestamp' => 'b',
            'x_amount' => 'c',
        );

        $expected = hash_hmac('md5', 'user^a^b^c^', 'key');

        $this->assertSame($expected, $this->request->createHash($data));
    }

    public function testSend()
    {
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());

        $redirectData = $response->getRedirectData();
        $this->assertSame('https://www.example.com/return', $redirectData['x_relay_url']);
    }

    // Issue #16 Support notifyUrl
    public function testSendNotifyUrl()
    {
        $this->request->setReturnUrl(null);
        $this->request->setNotifyUrl('https://www.example.com/return');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());

        $redirectData = $response->getRedirectData();
        $this->assertSame('https://www.example.com/return', $redirectData['x_relay_url']);
    }
}
