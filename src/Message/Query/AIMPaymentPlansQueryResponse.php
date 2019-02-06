<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\AbstractResponse;

/**
 * Authorize.Net AIM Response
 */
class AIMPaymentPlansQueryResponse extends AbstractQueryResponse
{
    public function __construct(AbstractRequest $request, $data)
    {
        // Strip out the xmlns junk so that PHP can parse the XML
        $xml = preg_replace('/<ARBGetSubscriptionListRequest[^>]+>/', '<ARBGetSubscriptionListRequest>', (string)$data);

        try {
            $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOWARNING);
        } catch (\Exception $e) {
            throw new InvalidResponseException();
        }

        if (!$xml) {
            throw new InvalidResponseException();
        }

        parent::__construct($request, $xml);
    }

    public function isSuccessful()
    {
        return 1 === $this->getResultCode();
    }

    public function getPlanData()
    {
        $result = $this->xml2array($this->data->subscriptionDetails, true);
        return $result['subscriptionDetails'][0]['subscriptionDetail'];
    }
}
