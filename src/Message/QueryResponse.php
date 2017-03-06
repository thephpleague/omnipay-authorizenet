<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Model\CardReference;
use Omnipay\AuthorizeNet\Model\TransactionReference;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Omnipay;

/**
 * Authorize.Net AIM Response
 */
class QueryResponse extends QueryBatchResponse
{

    public function getData()
    {
        $data = parent::getData();
        $result = array();
        /** @var \Omnipay\AuthorizeNet\AIMGateway $gateway */
        $gateway = Omnipay::create('AuthorizeNet_AIM');
        if (!empty($data)) {
            foreach ($data as $batch) {
                $gateway->setApiLoginId($this->request->getApiLoginId());
                $gateway->setHashSecret($this->request->getHashSecret());
                $gateway->setTransactionKey($this->request->getTransactionKey());
                $gateway->setDeveloperMode($this->request->getDeveloperMode());
                $data = array('batch_id' => $batch['batchId']);
                $dataResponse = $gateway->queryBatchDetail($data)->send();
                $transactions = $dataResponse->getData();
                foreach ($transactions as $transaction) {
                    $detailResponse = $gateway->queryDetail(array('transactionReference' => $transaction['transId']))
                        ->send();
                    $result[] = $detailResponse;
                }
            }
        }
        return $result;
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
