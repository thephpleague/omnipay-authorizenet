<?php

namespace Omnipay\AuthorizeNet\Message\Query;

/**
 * Authorize.Net AIM Abstract Request
 */

use Omnipay\AuthorizeNet\Message\AIMAbstractRequest;
use SimpleXMLElement;

abstract class AIMAbstractQueryRequest extends AIMAbstractRequest
{
    protected $limit = 1000;
    protected $offset = 1;

    /**
     * Disable validation check on the parent method.
     */
    protected function addTransactionType(SimpleXMLElement $data)
    {
        // NOOP
    }

    /**
     * Get Limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set Limit.
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Get offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set offset.
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        return $data;
    }
}
