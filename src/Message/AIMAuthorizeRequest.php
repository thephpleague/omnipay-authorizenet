<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMAuthorizeRequest extends AIMAbstractRequest
{
    protected $action = 'authOnlyTransaction';

    public function getData()
    {
        $this->validate('amount');
        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $this->addPayment($data);
        $this->addSolutionId($data);
        $this->addBillingData($data);
        $this->addCustomerIP($data);
        $this->addRetail($data);
        $this->addTransactionSettings($data);

        return $data;
    }

    protected function addPayment(\SimpleXMLElement $data)
    {
        /**
         * @link http://developer.authorize.net/api/reference/features/acceptjs.html Documentation on opaque data
         */
        if ($this->getOpaqueDataDescriptor() && $this->getOpaqueDataValue()) {
            $data->transactionRequest->payment->opaqueData->dataDescriptor = $this->getOpaqueDataDescriptor();
            $data->transactionRequest->payment->opaqueData->dataValue = $this->getOpaqueDataValue();
            return;
        }

        $this->validate('card');
        /** @var CreditCard $card */
        $card = $this->getCard();
        $card->validate();
        $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumber();
        $data->transactionRequest->payment->creditCard->expirationDate = $card->getExpiryDate('my');
        $data->transactionRequest->payment->creditCard->cardCode = $card->getCvv();
    }

    protected function addCustomerIP(\SimpleXMLElement $data)
    {
        $ip = $this->getClientIp();
        if (!empty($ip)) {
            $data->transactionRequest->customerIP = $ip;
        }
    }

    protected function addRetail(\SimpleXMLElement $data)
    {
        $deviceType = $this->getDeviceType();
        $marketType = $this->getMarketType();

        if (!isset($deviceType) && !isset($marketType)) {
            return;
        }

        if (!isset($deviceType) && isset($marketType)) {
            throw new InvalidRequestException();
        }

        if (isset($deviceType) && !isset($marketType)) {
            $marketType = "2";
        }

        if (!in_array($deviceType, [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10" ])) {
            throw new InvalidRequestException();
        }

        if (!in_array($marketType, [ "0", "1", "2" ])) {
            throw new InvalidRequestException();
        }

        $data->transactionRequest->retail->marketType = $marketType;
        $data->transactionRequest->retail->deviceType = $deviceType;
    }

    public function getDeviceType()
    {
        return $this->getParameter('deviceType');
    }

    public function setDeviceType($value)
    {
        return $this->setParameter('deviceType', $value);
    }
    
    public function getMarketType()
    {
        return $this->getParameter('marketType');
    }

    public function setMarketType($value)
    {
        return $this->setParameter('marketType', $value);
    }
}
