<?php

namespace Message;

use Omnipay\AuthorizeNet\Message\CIMAbstractRequest;

abstract class CIMAbstractCustomerProfileRequest extends CIMAbstractRequest
{
    const VALIDATION_MODE_TEST = 'testMode';
    const VALIDATION_MODE_LIVE = 'liveMode';
    const VALIDATION_MODE_NONE = 'none';

    public function setValidationMode($value)
    {
        return $this->setParameter('validationMode', $value);
    }

    public function getValidationMode()
    {
        $validationMode = $this->getParameter('validationMode');
        if($validationMode !== self::VALIDATION_MODE_NONE) {
            $validationMode = $this->getDeveloperMode() ? self::VALIDATION_MODE_TEST : self::VALIDATION_MODE_LIVE;
        }
        return $validationMode;
    }
}