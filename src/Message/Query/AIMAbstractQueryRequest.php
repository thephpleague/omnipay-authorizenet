<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\AuthorizeNet\Message\AIMAbstractRequest;

/**
 * Authorize.Net AIM Abstract Request
 */
abstract class AIMAbstractQueryRequest extends AIMAbstractRequest
{
    protected $limit = 1000;
    protected $offset = 1;

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
