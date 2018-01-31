<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest;
use Omnipay\AuthorizeNet\Message\AIMCaptureRequest;
use Omnipay\AuthorizeNet\Message\AIMPaymentPlanQueryResponse;
use Omnipay\AuthorizeNet\Message\AIMPurchaseRequest;
use Omnipay\AuthorizeNet\Message\QueryRequest;
use Omnipay\AuthorizeNet\Message\AIMRefundRequest;
use Omnipay\AuthorizeNet\Message\AIMVoidRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Authorize.Net AIM Class
 */
class AIMGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Authorize.Net AIM';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiLoginId'        => '',
            'transactionKey'    => '',
            'testMode'          => false,
            'developerMode'     => false,
            'hashSecret'        => '',
            'liveEndpoint'      => 'https://api2.authorize.net/xml/v1/request.api',
            'developerEndpoint' => 'https://apitest.authorize.net/xml/v1/request.api',
        );
    }

    public function getApiLoginId()
    {
        return $this->getParameter('apiLoginId');
    }

    public function setApiLoginId($value)
    {
        return $this->setParameter('apiLoginId', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }

    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }

    public function setDeveloperMode($value)
    {
        return $this->setParameter('developerMode', $value);
    }

    public function setHashSecret($value)
    {
        return $this->setParameter('hashSecret', $value);
    }

    public function getHashSecret()
    {
        return $this->getParameter('hashSecret');
    }

    public function setEndpoints($endpoints)
    {
        $this->setParameter('liveEndpoint', $endpoints['live']);
        return $this->setParameter('developerEndpoint', $endpoints['developer']);
    }

    public function getLiveEndpoint()
    {
        return $this->getParameter('liveEndpoint');
    }

    public function setLiveEndpoint($value)
    {
        return $this->setParameter('liveEndpoint', $value);
    }

    public function getDeveloperEndpoint()
    {
        return $this->getParameter('developerEndpoint');
    }

    public function setDeveloperEndpoint($value)
    {
        return $this->setParameter('developerEndpoint', $value);
    }

    public function getDuplicateWindow()
    {
        return $this->getParameter('duplicateWindow');
    }

    public function setDuplicateWindow($value)
    {
        return $this->setParameter('duplicateWindow', $value);
    }

    /**
     * @param array $parameters
     * @return AIMAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMCaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMCaptureOnlyRequest
     */
    public function captureOnly(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMCaptureOnlyRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMVoidRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMRefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMPaymentPlansQueryRequest
     */
    public function paymentPlansQuery(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\Query\AIMPaymentPlansQueryRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMPaymentPlanQueryResponse
     */
    public function paymentPlanQuery(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\Query\AIMPaymentPlanQueryRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return QueryResponse
     */
    public function query(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\Query\QueryRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return QueryBatchResponse
     */
    public function queryBatch(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\QueryBatchRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return QueryBatchDetailResponse
     */
    public function queryBatchDetail(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\QueryBatchDetailRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return QueryDetailResponse
     */
    public function queryDetail(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\QueryDetailRequest', $parameters);
    }
}
