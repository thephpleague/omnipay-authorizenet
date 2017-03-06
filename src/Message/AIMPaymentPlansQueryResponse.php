<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Model\CardReference;
use Omnipay\AuthorizeNet\Model\TransactionReference;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\AbstractResponse;

/**
 * Authorize.Net AIM Response
 */
class AIMPaymentPlansQueryResponse extends AbstractResponse
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

    /**
     * http://bookofzeus.com/articles/convert-simplexml-object-into-php-array/
     *
     * Convert a simpleXMLElement in to an array
     *
     * @todo this is duplicated from CIMAbstractResponse. Put somewhere shared.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return array
     */
    public function xml2array(\SimpleXMLElement $xml)
    {
        $arr = array();
        foreach ($xml as $element) {
            $tag = $element->getName();
            $e = get_object_vars($element);
            if (!empty($e)) {
                $arr[$tag][] = $element instanceof \SimpleXMLElement ? $this->xml2array($element) : $e;
            } else {
                $arr[$tag] = trim($element);
            }
        }

        return $arr;
    }
}
