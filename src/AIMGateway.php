<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest;
use Omnipay\AuthorizeNet\Message\AIMCaptureOnlyRequest;
use Omnipay\AuthorizeNet\Message\AIMCaptureRequest;
use Omnipay\AuthorizeNet\Message\Query\AIMPaymentPlanQueryResponse;
use Omnipay\AuthorizeNet\Message\AIMPurchaseRequest;
use Omnipay\AuthorizeNet\Message\AIMRefundRequest;
use Omnipay\AuthorizeNet\Message\AIMVoidRequest;
use Omnipay\AuthorizeNet\Message\Query\AIMPaymentPlansQueryRequest;
use Omnipay\AuthorizeNet\Message\Query\QueryResponse;
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
            'apiLoginId'        => null,
            'transactionKey'    => null,
            'testMode'          => false,
            'developerMode'     => false,
            'hashSecret'        => null,
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

    public function getSignatureKey()
    {
        return $this->getParameter('signatureKey');
    }

    public function setSignatureKey($value)
    {
        return $this->setParameter('signatureKey', $value);
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
        return $this->createRequest(
            AIMAuthorizeRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return AIMCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest(
            AIMCaptureRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return AIMCaptureOnlyRequest
     */
    public function captureOnly(array $parameters = array())
    {
        return $this->createRequest(
            AIMCaptureOnlyRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return AIMPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(
            AIMPurchaseRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return AIMVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest(
            AIMVoidRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return AIMRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest(
            AIMRefundRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return AIMPaymentPlansQueryRequest
     */
    public function paymentPlansQuery(array $parameters = array())
    {
        return $this->createRequest(
            Message\Query\AIMPaymentPlansQueryRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function paymentPlanQuery(array $parameters = array())
    {
        return $this->createRequest(
            Message\Query\AIMPaymentPlanQueryRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return QueryResponse
     */
    public function query(array $parameters = array())
    {
        return $this->createRequest(
            Message\Query\QueryRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return Message\Query\QueryBatchRequest
     */
    public function queryBatch(array $parameters = array())
    {
        return $this->createRequest(
            Message\Query\QueryBatchRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return Message\Query\QueryBatchDetailRequest
     */
    public function queryBatchDetail(array $parameters = array())
    {
        return $this->createRequest(
            Message\Query\QueryBatchDetailRequest::class,
            $parameters
        );
    }

    /**
     * @param array $parameters
     * @return Message\Query\QueryDetailRequest
     */
    public function queryDetail(array $parameters = array())
    {
        return $this->createRequest(
            Message\Query\QueryDetailRequest::class,
            $parameters
        );
    }
}
