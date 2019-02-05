<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Omnipay;

/**
 * Authorize.Net AIM Response
 */
class AIMPaymentPlanQueryResponse extends AbstractQueryResponse
{
    protected $subscription;
    protected $profile;

    public function __construct(AbstractRequest $request, $data)
    {
        // Strip out the xmlns junk so that PHP can parse the XML
        $xml = preg_replace(
            '/<ARBGetSubscriptionRequest[^>]+>/',
            '<ARBGetSubscriptionRequest>',
            (string)$data
        );

        try {
            $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOWARNING);
        } catch (\Exception $e) {
            throw new InvalidResponseException();
        }

        if (!$xml) {
            throw new InvalidResponseException();
        }

        parent::__construct($request, $xml);
        $result = $this->xml2array($this->data->subscription, true);
        $this->subscription = $result['subscription'][0];
    }

    public function isSuccessful()
    {
        return 1 === $this->getResultCode();
    }

    public function getData()
    {
        return $this->subscription;
    }

    public function getRecurStartDate()
    {
        return $this->subscription['paymentSchedule']['interval']['startDate'];
    }

    public function getRecurInstallmentLimit()
    {
        return $this->subscription['paymentSchedule']['interval']['totalOccurrences'];
    }

    public function getRecurrenceInterval()
    {
        return $this->subscription['paymentSchedule']['interval'][0]['length'];
    }

    public function getRecurAmount()
    {
        return $this->subscription['amount'];
    }

    public function getRecurReference()
    {
        return $this->subscription;
    }

    public function getContactReference()
    {
        $profileID = $this->subscription['profile'][0]['customerProfileId'];
        $gateway = $gateway = Omnipay::create('AuthorizeNet_CIM');
        $gateway->setApiLoginId($this->request->getApiLoginId());
        $gateway->setHashSecret($this->request->getHashSecret());
        $gateway->setTransactionKey($this->request->getTransactionKey());
        $data = array(
            'customerProfileId' => $profileID,
            'customerPaymentProfileId' =>
                $this->subscription['profile'][0]['paymentProfile'][0]['customerPaymentProfileId'],
        );
        $dataResponse = $gateway->getProfile($data)->send();
        return $dataResponse->getCustomerId();
    }

    /**
     * @todo formalise options.
     *
     * @return mixed
     */
    public function getRecurStatus()
    {
        return $this->subscription['paymentSchedule']['interval'][0]['status'];
    }

    public function getRecurrenceUnit()
    {
        $interval = $this->subscription['paymentSchedule']['interval'][0]['unit'];
        $options = array(
            'months' => 'month',
        );
        return $options[$interval];
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
